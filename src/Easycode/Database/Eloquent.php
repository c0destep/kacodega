<?php

declare(strict_types=1);

namespace Easycode\Database;

use Easycode\Application\EasyApp;
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
            'driver' => EasyApp::environment('DB_DRIVER'),
            'host' => EasyApp::environment('DB_HOST'),
            'database' => EasyApp::environment('DB_NAME'),
            'username' => EasyApp::environment('DB_USER'),
            'password' => EasyApp::environment('DB_PASS'),
            'charset' => EasyApp::environment('DB_CHARSET') ?? 'utf8',
            'collation' => EasyApp::environment('DB_COLLATION') ?? 'utf8_unicode_ci',
            'prefix' => EasyApp::environment('DB_PREFIX') ?? '',
        ]);
        $capsule->setEventDispatcher(new Dispatcher(new Container()));
        $capsule->setAsGlobal();
        $capsule->bootEloquent();
    }
}