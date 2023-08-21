<?php

if (! function_exists('create_log_config')) {
    function create_log_config(string $name, string $path = null): array
    {
        $path = $path ?? $name;

        return [
            "{$name}-daily" => [
                'driver' => 'daily',
                'path' => storage_path("logs/{$path}/{$name}.log"),
                'level' => env('LOG_LEVEL', 'debug'),
                'days' => env('LOG_DAILY_DAYS', 14),
            ],

            "{$name}-elastic" => [
                'driver' => 'monolog',
                'handler' => \Monolog\Handler\ElasticsearchHandler::class,
                'level' => 'debug',
                'formatter' => \Monolog\Formatter\ElasticsearchFormatter::class,
                'formatter_with' => [
                    'index' => env('
                        ELASTIC_LOGS_INDEX',
                        strtolower(preg_replace('/\s|\-/', '_', env('APP_NAME')))
                    )."-{$name}",
                    'type' => '_doc',
                ],
            ],

            "{$name}-stack" => [
                'driver' => 'stack',
                'channels' => ["{$name}-daily", "{$name}-elastic"],
                'ignore_exceptions' => false,
            ],
        ];
    }
}

if (! function_exists('get_log_channel')) {
    function get_log_channel(string $name): string
    {
        return $name.'-'.config('logging.default');
    }
}

if (! function_exists('modify_log_record')) {
    function modify_log_record(array &$record): void
    {
        if ($record['_index'] == config('services.elastic.index').'-'.'request') {
            $record['context']['json'] = json_encode([
                'request' => $record['context']['request'],
                'response' => $record['context']['response'],
            ]);

            unset($record['context']['request'], $record['context']['response']);
        }
    }
}
