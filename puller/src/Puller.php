<?php

namespace As247\Puller;

use Illuminate\Container\Container;
use Illuminate\Support\InteractsWithTime;
use Illuminate\Support\Str;

abstract class Puller implements Contracts\Puller
{
    use InteractsWithTime;
    /**
     * The IoC container instance.
     *
     * @var \Illuminate\Container\Container
     */
    protected $container;

    /**
     * The connection name for the queue.
     *
     * @var string
     */
    protected $connectionName;

    /**
     * @var int The number of seconds to remove old messages
     */
    protected $removeAfter;

    abstract protected function store(Message $message);
    abstract protected function fetch($channel,$token,$size=10);
    protected function createMessage($channel,$event,$data,$expiredAt=null){
        $message = new Message();
        $message->token = $this->generateUniqueToken();
        $message->channel = $channel;
        $message->payload = [$event,$data];
        $message->expired_at = $this->availableAt($expiredAt??$this->removeAfter);
        $message->created_at = $this->currentTime();
        return $message;
    }

    public function push($channel,$event,$data=[],$expiredAt=null){
        try {
            $message = $this->createMessage($channel, $event, $data, $expiredAt);
            return $this->store($message);
        }catch (\Exception $exception){
            $message = $this->createMessage($channel, $event, $data, $expiredAt);
            return $this->store($message);
        }

    }
    public function pull($channel,$token,$size=10){
        $messages=$this->fetch($channel,$token,$size);
        if(!$messages){
            return new MessageCollection();
        }
        return new MessageCollection($messages);
    }

    protected function generateUniqueToken(){
        return Str::random(32).Str::replace('-', '', Str::uuid());
    }

    /**
     * Get the connection name for the queue.
     *
     * @return string
     */
    public function getConnectionName()
    {
        return $this->connectionName;
    }

    /**
     * Set the connection name for the queue.
     *
     * @param  string  $name
     * @return $this
     */
    public function setConnectionName($name)
    {
        $this->connectionName = $name;

        return $this;
    }

    /**
     * Get the container instance being used by the connection.
     *
     * @return \Illuminate\Container\Container
     */
    public function getContainer()
    {
        return $this->container;
    }

    /**
     * Set the IoC container instance.
     *
     * @param  \Illuminate\Container\Container  $container
     * @return void
     */
    public function setContainer(Container $container)
    {
        $this->container = $container;
    }
}
