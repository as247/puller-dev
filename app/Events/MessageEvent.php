<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Carbon;

class MessageEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $content;
    public $name;
    public $time;
    public $id;
    /**
     * Create a new event instance.
     */
    public function __construct($message,$name='')
    {
        $this->id=uniqid();
        $this->content=$message;
        if(!$name){
            $name='Anonymous';
        }
        $name.="[".request()->ip()."]";
        $this->name=$name;
        $this->time=Carbon::now('Asia/Ho_Chi_Minh')->toDateTimeString();
    }
    public function broadcastOn()
    {
        return [
            new PrivateChannel('chat'),
        ];
    }
}
