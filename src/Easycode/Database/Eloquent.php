<?php

declare(strict_types=1);

namespace Easycode\Database;

use Illuminate\Container\Container;
use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Events\Dispatcher;

class Eloquent implements DriverImplements
{

    /**
     * @return void
     */
    public function createConnection(): void
    {
        $capsule = new Capsule();
        $capsule->addConnection([
            'driver' => $_ENV['DB_DRIVER'],
            'host' => $_ENV['DB_HOST'],
            'database' => $_ENV['DB_NAME'],
            'username' => $_ENV['DB_USER'],
            'password' => $_ENV['DB_PASS'],
            'charset' => $_ENV['DB_CHARSET'] ?? 'utf8',
            'collation' => $_ENV['DB_COLLATION'] ?? 'utf8_unicode_ci',
            'prefix' => $_ENV['DB_PREFIX'] ?? '',
        ]);
        $capsule->setEventDispatcher(new Dispatcher(new Container()));
        $capsule->setAsGlobal();
        $capsule->bootEloquent();
    }
}