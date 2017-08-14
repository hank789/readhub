<?php

namespace App\Listeners;

use App\Events\CommentCreated;
use App\Events\CommentWasCreated;
use App\Notifications\CommentReplied;
use App\Notifications\SubmissionReplied;
use App\Permissions;
use App\Traits\CachableCategory;
use App\Traits\CachableSubmission;
use App\Traits\CachableUser;
use App\Traits\UsernameMentions;
use Illuminate\Contracts\Queue\ShouldQueue;


class NewComment implements ShouldQueue
{
    use CachableUser, CachableCategory, CachableSubmission, Permissions, UsernameMentions;

    /**
     * 任务最大尝试次数
     *
     * @var int
     */
    public $tries = 1;

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param CommentWasCreated $event
     *
     * @return void
     */
    public function handle(CommentWasCreated $event)
    {
        $this->updateUserCommentsCount($event->comment->user_id);
        $this->updateCategoryCommentsCount($event->comment->category_id);

        // update submission
        $event->submission->update([
            'comments_number' => ($event->submission->comments_number + 1),
        ]);
        $this->putSubmissionInTheCache($event->submission);

        // if the commenter is banned from submitting to this cateogry (or "everywhere") we
        // soft-delete the comment without letting him know. This should keep spammers
        // busy over nothing.
        if ($this->isUserBanned($event->author->id, $event->submission->category_name)) {
            $event->comment->delete();

            return;
        }

        // broadcast the comment to the people online in the conversation
        event(new CommentCreated($event->comment));

        if (isset($event->parentComment) && !$this->mustBeOwner($event->parentComment,$event->author)) {
            $event->parentComment->notifiable->notify(new CommentReplied($event->submission, $event->comment));
        } elseif (!$this->mustBeOwner($event->submission,$event->author)) {
            $event->submission->notifiable->notify(new SubmissionReplied($event->submission, $event->comment));
        }

        $this->handleMentions($event->comment, $event->submission);

        slackNotification($event->comment->owner->username,'文章有新的评论', $event->comment->body,[],config('app.url').'/c/'.$event->submission->category_name.'/'.$event->submission->slug);

    }
}
