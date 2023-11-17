<?php

namespace As247\Puller;

use Illuminate\Database\Connection;

class DatabasePuller extends Puller
{
    /**
     * The database connection instance.
     *
     * @var \Illuminate\Database\Connection
     */
    protected $database;

    /**
     * The database table that holds the jobs.
     *
     * @var string
     */
    protected $table;


    public function __construct(Connection $database,
                                           $table,
                                            $removeAfter = 60    )
    {
        $this->database= $database;
        $this->table = $table;
        $this->removeAfter = $removeAfter;
    }

    function store(Message $message){
        $this->database->table($this->table)->insert([
            'channel'=>$message->channel,
            'payload'=>json_encode($message->payload),
            'expired_at'=>$message->expired_at,
            'created_at'=>$message->created_at,
        ]);
    }


}
