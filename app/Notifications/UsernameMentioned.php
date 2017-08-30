<?php

namespace App\Notifications;

use App\Channels\InwehubChannel;
use App\Comment;
use App\Submission;
use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class UsernameMentioned extends Notification implements ShouldBroadcast
{
    use Queueable;

    public $user;
    public $submission;
    protected $comment;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(Submission $submission, Comment $comment)
    {
        $this->user = $comment->owner;
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
        if ($notifiable->settings['notify_mentions'] && $notifiable->username != $this->user->username) {
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
        //        return (new MailMessage)
//                    ->line('The introduction to the notification.')
//                    ->action('Notification Action', url('/'))
//                    ->line('Thank you for using our application!');
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
            'url'    => '/c/'.$this->submission->category_name.'/'.$this->submission->slug,
            'name'   => $this->user->username,
            'avatar' => $this->user->avatar,
            'body'   => '@'.$this->user->username.' 提到了你 "'.$this->submission->title.'"',
        ];
    }

    public function toInwehub($notifiable){
        return [
            'url'    => '/c/'.$this->submission->category_name.'/'.$this->submission->slug,
            'name'   => $this->user->username,
            'avatar' => $this->user->avatar,
            'title'  => $this->user->username.'提到了你',
            'body'   => $this->comment->body,
            'comment_id' => $this->comment->id,
            'extra_body' => '原文：'.$this->submission->title
        ];
    }
}
