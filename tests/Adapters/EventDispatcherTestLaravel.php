<?php

namespace Tmdb\Laravel\Tests\Adapters;

use Tmdb\Laravel\Adapters\EventDispatcherLaravel as AdapterDispatcher;

class EventDispatcherTestLaravel extends AbstractEventDispatcherTest
{
    protected function createEventDispatcher()
    {
        $this->laravel = $this->prophesize('Illuminate\Events\Dispatcher');
        $this->symfony = $this->prophesize('Symfony\Component\EventDispatcher\EventDispatcher');

        return new AdapterDispatcher(
            $this->laravel->reveal(),
            $this->symfony->reveal()
        );
    }
}
