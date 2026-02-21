<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;



class TestRedisJob implements ShouldQueue
{
    public function handle()
    {
        sleep(60);
    }
}
