<?php

namespace As247\Puller;

class Message
{
    public $id;
    public $token;
    public $channel;
    public $payload;
    public $created_at;
    public $expired_at;

    public function __construct($message=null)
    {
        if(!$message){
            return;
        }
        $this->id=$message->id;
        $this->token=$message->token;
        $this->channel=$message->channel;
        $this->payload=json_decode($message->payload);
        $this->created_at=$message->created_at;
        $this->expired_at=$message->expired_at;
    }

    function toDatabase(){
        return [
            'token'=>$this->token,
            'channel'=>$this->channel,
            'payload'=>$this->payload,
            'created_at'=>$this->created_at,
            'expired_at'=>$this->expired_at,
        ];
    }
}
