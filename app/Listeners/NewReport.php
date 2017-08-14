<?php

namespace App\Listeners;

use App\Events\ReportWasCreated;
use App\Notifications\CommentReported;
use App\Notifications\SubmissionReported;
use App\Traits\CachableCategory;
use Illuminate\Contracts\Queue\ShouldQueue;


class NewReport implements ShouldQueue
{
    use CachableCategory;

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
     * @param ReportWasCreated $event
     *
     * @return void
     */
    public function handle(ReportWasCreated $event)
    {
        $category = $this->getCategoryById($event->report->category_id);

        $category_mods = $category->moderators;

        foreach ($category_mods as $user) {
            if ($event->report->reportable_type == 'App\Submission') {
                $user->notify(new SubmissionReported($category, $event->report->submission));
            } elseif ($event->report->reportable_type == 'App\Comment') {
                $user->notify(new CommentReported($category, $event->report->comment));
            }
        }

        $slackFields = [];
        $slackFields[] = [
            'title' => '举报者',
            'value' => $event->report->reporter->username
        ];
        $slackFields[] = [
            'title' => '举报原因',
            'value' => $event->report->subject
        ];
        $slackFields[] = [
            'title' => '举报描述',
            'value' => $event->report->description
        ];

        if ($event->report->reportable_type == 'App\Submission') {
            $slackFields[] = [
                'title' => '文章标题',
                'value' => $event->report->submission->title
            ];
            slackNotification($event->report->submission->owner->username,'文章被举报','',$slackFields,config('app.url').'/c/'.$event->report->submission->category_name.'/'.$event->report->submission->slug,'warning');
        } elseif ($event->report->reportable_type == 'App\Comment') {
            $slackFields[] = [
                'title' => '评论内容',
                'value' => $event->report->comment->body
            ];
            slackNotification($event->report->comment->owner->username,'评论被举报','',$slackFields,config('app.url').'/c/'.$event->report->comment->submission->category_name.'/'.$event->report->comment->submission->slug,'warning');
        }


    }
}
