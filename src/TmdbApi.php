<?php

namespace Tmdb\Laravel;

use Illuminate\Contracts\Config\Repository;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Psr\Log\LogLevel;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Tmdb\Client;
use Tmdb\Event\BeforeHydrationEvent;
use Tmdb\Event\BeforeRequestEvent;
use Tmdb\Event\HttpClientExceptionEvent;
use Tmdb\Event\Listener\Logger\LogApiErrorListener;
use Tmdb\Event\Listener\Logger\LogHttpMessageListener;
use Tmdb\Event\Listener\Logger\LogHydrationListener;
use Tmdb\Event\Listener\Psr6CachedRequestListener;
use Tmdb\Event\Listener\Request\AcceptJsonRequestListener;
use Tmdb\Event\Listener\Request\ApiTokenRequestListener;
use Tmdb\Event\Listener\Request\ContentTypeJsonRequestListener;
use Tmdb\Event\Listener\Request\UserAgentRequestListener;
use Tmdb\Event\Listener\RequestListener;
use Tmdb\Event\RequestEvent;
use Tmdb\Event\ResponseEvent;
use Tmdb\Event\TmdbExceptionEvent;
use Tmdb\Formatter\HttpMessage\FullHttpMessageFormatter;
use Tmdb\Formatter\Hydration\SimpleHydrationFormatter;
use Tmdb\Formatter\TmdbApiException\SimpleTmdbApiExceptionFormatter;
use Tmdb\Laravel\Adapters\EventDispatcherAdapter;
use Tmdb\Token\Api\ApiToken;
use Tmdb\Token\Api\BearerToken;

class TmdbApi
{
    protected Client $client;

    protected EventDispatcherAdapter $eventDispatcher;

    protected array $config;

    public function __construct(Repository $config)
    {
        $this->config = $config['tmdb'];
        $this->eventDispatcher = app()->make(EventDispatcherAdapter::class);
        $this->client = new Client(array_merge(
            $this->config['options'],
            [
                'api_token' => $this->getToken(),
                'event_dispatcher' => ['adapter' => $this->eventDispatcher],
            ]
        ));

        $this->addListeners();

        if (config('tmdb.cache.enabled')) {
            $this->enableCache();
        }

        if (config('tmdb.log.enabled')) {
            $this->enableLog();
        }
    }

    /**
     * Add the listeners to the event dispatcher.
     *
     * @return void
     */
    protected function addListeners(): self
    {
        $this->eventDispatcher->addListener(
            RequestEvent::class,
            new RequestListener($this->client->getHttpClient(), $this->eventDispatcher)
        );

        $this->eventDispatcher->addListener(
            BeforeRequestEvent::class,
            new ApiTokenRequestListener($this->getToken())
        );

        $this->eventDispatcher->addListener(
            BeforeRequestEvent::class,
            new AcceptJsonRequestListener()
        );

        $this->eventDispatcher->addListener(
            BeforeRequestEvent::class,
            new ContentTypeJsonRequestListener()
        );

        $this->eventDispatcher->addListener(
            BeforeRequestEvent::class,
            new UserAgentRequestListener()
        );

        return $this;
    }

    /**
     * Return the API / Bearer token.
     *
     * @return ApiToken
     */
    protected function getToken(): ApiToken
    {
        if (config('tmdb.token.bearer_token')) {
            return new BearerToken(config('tmdb.token.bearer_token'));
        } else {
            return new ApiToken(config('tmdb.token.api_key'));
        }
    }

    /**
     * Instantiate the cache.
     *
     * @return $this
     */
    public function enableCache(): self
    {
        $cache = new FilesystemAdapter(
            'php-tmdb',
            config('tmdb.cache.lifetime'),
            config('tmdb.cache.path', storage_path('tmdb/cache'))
        );

        /**
         * The full setup makes use of the Psr6CachedRequestListener.
         *
         * Required event listeners and events to be registered with the PSR-14 Event Dispatcher.
         */
        $requestListener = new Psr6CachedRequestListener(
            $this->client->getHttpClient(),
            $this->eventDispatcher,
            $cache,
            $this->client->getHttpClient()->getPsr17StreamFactory(),
        );

        $this->eventDispatcher->addListener(RequestEvent::class, $requestListener);
        $this->eventDispatcher->addListener(
            BeforeRequestEvent::class,
            new ApiTokenRequestListener($this->client->getToken())
        );
        $this->eventDispatcher->addListener(BeforeRequestEvent::class, new AcceptJsonRequestListener());
        $this->eventDispatcher->addListener(BeforeRequestEvent::class, new ContentTypeJsonRequestListener());

        return $this;
    }

    /**
     * Instantiate the log.
     *
     * @return $this
     */
    public function enableLog(): self
    {
        $logger = new Logger(
            'php-tmdb',
            [
                new StreamHandler(
                    config('tmdb.log.path', storage_path('logs/tmdb.log')),
                    LogLevel::DEBUG
                )
            ]
        );

        $requestLoggerListener = new LogHttpMessageListener(
            $logger,
            new FullHttpMessageFormatter()
        );

        $this->eventDispatcher->addListener(BeforeRequestEvent::class, $requestLoggerListener);
        $this->eventDispatcher->addListener(ResponseEvent::class, $requestLoggerListener);
        $this->eventDispatcher->addListener(HttpClientExceptionEvent::class, $requestLoggerListener);

        $this->eventDispatcher->addListener(
            BeforeHydrationEvent::class,
            new LogHydrationListener(
                $logger,
                new SimpleHydrationFormatter(),
                // Add the json data passed for each hydration, on local environments. Do NOT enable on production.
                app()->environment('local')
            )
        );

        $this->eventDispatcher->addListener(
            TmdbExceptionEvent::class,
            new LogApiErrorListener($logger, new SimpleTmdbApiExceptionFormatter())
        );

        return $this;
    }

    /**
     * Return the client.
     *
     * @return Client
     */
    public function getClient(): Client
    {
        return $this->client;
    }
}
