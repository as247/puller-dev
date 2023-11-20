<?php

namespace As247\Puller;

use Illuminate\Database\ConnectionInterface;
use As247\Puller\Exceptions\InvalidTokenException;
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


    public function __construct(ConnectionInterface $database,
                                           $table,
                                            $removeAfter = 60    )
    {
        $this->database= $database;
        $this->table = $table;
        $this->removeAfter = $removeAfter;
    }

    function store(Message $message){
        $id=$this->database->table($this->table)->insertGetId($message->toDatabase());
        $message->id = $id;
        return $message;
    }
    function fetch($channel, $token, $size = 10)
    {
        $id=$this->database->table($this->table)
            ->select('id')
            ->where('token',$token)
            ->where('channel',$channel)
            ->limit(1)
            ->value('id');
        if(!$id){
            throw new InvalidTokenException('Invalid token');
        }

        return $this->database->table($this->table)
            ->where('channel',$channel)
            ->where('id','>',$id)
            ->where('payload','<>','')
            ->orderBy('id','asc')
            ->limit($size)
            ->get();
    }
    function purge(){
        $this->database->table($this->table)
            ->where('expired_at','<',now())
            ->delete();
    }
    function lastToken($channel){
        return $this->database->table($this->table)
            ->select('token')
            ->where('channel',$channel)
            ->orderBy('id','desc')
            ->limit(1)
            ->value('token');
    }


}
