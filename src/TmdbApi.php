<?php

namespace Tmdb\Laravel;

use Illuminate\Contracts\Config\Repository;
use Tmdb\Client;
use Tmdb\Laravel\Adapters\EventDispatcherAdapter;

class TmdbApi
{
    protected Client $client;

    protected array $config;

    public function __construct(Repository $config)
    {
        $this->config = $config['tmdb'];

        $options = array_merge(
            $this->config['options'],
            [
                'api_token' => $this->config['api_key'],
                'event_dispatcher' => app()->make(EventDispatcherAdapter::class),
            ]
        );

        $this->client = new Client($options);
    }

    public function getClient(): Client
    {
        return $this->client;
    }
}
