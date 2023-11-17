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
                                           $table)
    {
        $this->database= $database;
        $this->table = $table;
    }
}
