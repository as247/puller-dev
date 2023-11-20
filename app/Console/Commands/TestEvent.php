<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class TestEvent extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:test-event';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $message=app('puller')->push('test','my-event', ['test' => 'test','time'=>time()]);
        $this->info('Pushed message: '.json_encode($message));
    }
}
