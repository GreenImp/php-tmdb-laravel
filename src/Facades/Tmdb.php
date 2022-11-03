<?php

namespace Tmdb\Laravel\Facades;

use Illuminate\Support\Facades\Facade;

class Tmdb extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return 'Tmdb\Client';
    }

}
