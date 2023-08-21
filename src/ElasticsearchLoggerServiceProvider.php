<?php

namespace QuetzalStudio\ElasticsearchLogger;

use Elastic\Elasticsearch\Client;
use Elastic\Elasticsearch\ClientBuilder;
use Illuminate\Support\ServiceProvider;
use Monolog\Handler\ElasticsearchHandler as MonologElasticsearchHandler;
use QuetzalStudio\ElasticsearchLogger\Handler\ElasticsearchHandler;

class ElasticsearchLoggerServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config.php', 'services');
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->app->bind(Client::class, function () {
            return ClientBuilder::create()
                ->setHosts([config('services.elastic.host')])
                ->setApiKey(config('services.elastic.api_key'))
                ->build();
        });

        $this->app->bind(MonologElasticsearchHandler::class, function ($app) {
            return new ElasticsearchHandler($app->make(Client::class));
        });
    }
}
