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

    function toDatabase(){
        return [
            'token'=>$this->token,
            'channel'=>$this->channel,
            'payload'=>json_encode($this->payload),
            'created_at'=>$this->created_at,
            'expired_at'=>$this->expired_at,
        ];
    }
}
