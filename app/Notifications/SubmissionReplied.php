<?php

namespace App\Notifications;

use App\Channels\InwehubChannel;
use App\Comment;
use App\Submission;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Queue\SerializesModels;

class SubmissionReplied extends Notification implements ShouldBroadcast
{
    use Queueable, InteractsWithSockets, SerializesModels;

    protected $submission;
    protected $comment;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(Submission $submission, Comment $comment)
    {
        $this->submission = $submission;
        $this->comment = $comment;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param mixed $notifiable
     *
     * @return array
     */
    public function via($notifiable)
    {
        if ($notifiable->settings['notify_submissions_replied']) {
            return ['database', 'broadcast', InwehubChannel::class];
        }

        return [];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param mixed $notifiable
     *
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage())
                    ->title($this->comment->user->username.' replied:')
                    ->subject('Your submission "'.$this->submission->title.'" just got a new comment.')
                    ->line('"'.$this->comment->body.'"')
                    ->action('Reply', config('app.url').'/'.$this->submission->slug)
                    ->line('Thank you for being a part of our alpha program!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @param mixed $notifiable
     *
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            'url'    => '/c/'.$this->submission->category_id.'/'.$this->submission->slug.'?comment='.$this->comment->id,
            'name'   => $this->comment->owner->username,
            'avatar' => $this->comment->owner->avatar,
            'body'   => '@'.$this->comment->owner->username.' 回复了文章 "'.$this->submission->title.'"',
        ];
    }

    public function toInwehub($notifiable){
        return [
            'url'    => '/c/'.$this->submission->category_id.'/'.$this->submission->slug.'?comment='.$this->comment->id,
            'name'   => $this->comment->owner->username,
            'avatar' => $this->comment->owner->avatar,
            'title'  => $this->comment->owner->username.'回复了文章',
            'comment_id' => $this->comment->id,
            'body'   => $this->comment->body,
            'extra_body' => '原文：'.$this->submission->title
        ];
    }

    /**
     * Get the broadcastable representation of the notification.
     *
     * @param mixed $notifiable
     *
     * @return BroadcastMessage
     */
    public function toBroadcast($notifiable)
    {
        return new BroadcastMessage([
            'data'       => $this->toArray($notifiable),
            'created_at' => now(),
            'read_at'    => null,
        ]);
    }
}
