<?php

namespace App\Notifications;

use App\Category;
use App\Channels\InwehubChannel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Queue\SerializesModels;

class CommentReported extends Notification implements ShouldBroadcast
{
    use Queueable, InteractsWithSockets, SerializesModels;

    protected $category;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(Category $category)
    {
        $this->category = $category;
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
        return ['database', 'broadcast', InwehubChannel::class];
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
                    ->line('The introduction to the notification.')
                    ->action('Notification Action', url('/'))
                    ->line('Thank you for using our application!');
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
            'url'    => '/c/'.$this->category->id.'/mod/reports/comments/',
            'name'   => $this->category->name,
            'avatar' => $this->category->avatar,
            'body'   => '回复被举报了 #'.$this->category->name,
        ];
    }

    public function toInwehub($notifiable){
        return [
            'url'    => '/c/'.$this->category->id.'/mod/reports/comments/',
            'avatar' => $this->category->avatar,
            'title'  => '回复被举报了',
            'body'   => '#'.$this->category->name,
            'extra_body' => ''
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
