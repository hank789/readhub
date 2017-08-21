<?php namespace App\Channels;
/**
 * @author: wanghui
 * @date: 2017/8/21 下午2:44
 * @email: wanghui@yonglibao.com
 */
use App\Jobs\NotifyInwehub;
use Illuminate\Notifications\Notification;
class InwehubChannel {

    /**
     * 发送通知给Inwehub
     *
     * @param  mixed  $notifiable
     * @param  \Illuminate\Notifications\Notification  $notification
     * @return void
     */
    public function send($notifiable, Notification $notification)
    {
        $message = $notification->toInwehub($notifiable);
        $className = str_replace('App\\Notifications\\','App\\Notifications\\Readhub\\',get_class($notification));
        dispatch((new NotifyInwehub($notifiable->id,$className,$message))->onQueue('inwehub:default'));
    }
}