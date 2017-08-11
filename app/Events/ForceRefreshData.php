<?php namespace App\Events;
/**
 * @author: wanghui
 * @date: 2017/8/11 下午3:19
 * @email: wanghui@yonglibao.com
 */


use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class ForceRefreshData implements ShouldBroadcast
{
    use InteractsWithSockets;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct()
    {

    }

    /**
     *   Get the channels the event should broadcast on.
     *
     *   @return Channel|array
     */
    public function broadcastOn()
    {
        return ['refresh.store'];
    }
}
