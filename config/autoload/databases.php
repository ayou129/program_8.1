<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://hyperf.wiki
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */
use function Hyperf\Support\env;

return [
    'default' => [
        'driver' => env('DB_DRIVER', 'mysql'),
        'host' => env('DB_HOST', 'localhost'),
        'database' => env('DB_DATABASE', 'hyperf'),
        'port' => env('DB_PORT', 3306),
        'username' => env('DB_USERNAME', 'root'),
        'password' => env('DB_PASSWORD', ''),
        'charset' => env('DB_CHARSET', 'utf8'),
        'collation' => env('DB_COLLATION', 'utf8_unicode_ci'),
        'prefix' => env('DB_PREFIX', ''),
        'pool' => [
            'min_connections' => 100,
            'max_connections' => 4500,
            'connect_timeout' => 10.0,
            'wait_timeout' => 3.0,
            'heartbeat' => -1,
            'max_idle_time' => (float) env('DB_MAX_IDLE_TIME', 60),
        ],
        'commands' => [
            'gen:model' => [
                'path' => 'app/Model',
                'force_casts' => true,
                'inheritance' => 'BaseModel',
                'refresh_fillable' => true,
                'uses' => '',
                # App\Model\BaseModel
                'visitors' => [
                    Hyperf\Database\Commands\Ast\ModelRewriteKeyInfoVisitor::class,
                    Hyperf\Database\Commands\Ast\ModelRewriteSoftDeletesVisitor::class,
                    Hyperf\Database\Commands\Ast\ModelRewriteTimestampsVisitor::class,
                    // Hyperf\Database\Commands\Ast\ModelRewriteGetterSetterVisitor::class,
                    // Hyperf\Database\Commands\Ast\ModelUpdateVisitor::class,
                ],
            ],
        ],
        // 'cache' => [
        //     'handler' => \Hyperf\ModelCache\Handler\RedisHandler::class,
        //     'cache_key' => 'mc:%s:m:%s:%s:%s',
        //     'prefix' => 'default',
        //     'ttl' => 3600 * 24,
        //     'empty_model_ttl' => 3600,
        //     'load_script' => true,
        //     'use_default_value' => true,
        // ],
    ],
];
