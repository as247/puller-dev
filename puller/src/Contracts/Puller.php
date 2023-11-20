<?php

namespace As247\Puller\Contracts;

use Illuminate\Container\Container;

interface Puller
{
    /**
     * Get the connection name for the queue.
     *
     * @return string
     */
    public function getConnectionName();

    /**
     * Set the connection name for the queue.
     *
     * @param  string  $name
     * @return $this
     */
    public function setConnectionName($name);

    /**
     * Set the IoC container instance.
     *
     * @param  \Illuminate\Container\Container  $container
     * @return void
     */
    public function setContainer(Container $container);

    /**
     * Get the container instance being used by the connection.
     *
     * @return \Illuminate\Container\Container
     */
    public function getContainer();

    public function pull($channel,$token,$size=10);
    public function getToken($channel);

    /**
     * @param string $channel
     * @param string $event
     * @param array $data
     * @param \DateTimeInterface|\DateInterval|int|null $ttl
     * @return mixed
     */
    public function push($channel,$event='',$data=[],$ttl=null);
}
