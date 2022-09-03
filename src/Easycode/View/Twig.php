<?php

declare(strict_types=1);

namespace Easycode\View;

use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use Twig\TwigFunction;

class Twig
{
    /**
     * @var Twig
     */
    private static Twig $instance;
    /**
     * @var Environment
     */
    private Environment $twigInstance;

    /**
     * @param string $pathViews
     * @param string|bool $cache
     * @param bool $debug
     */
    private function __construct(string $pathViews, string|bool $cache = false, bool $debug = false)
    {
        $this->twigInstance = new Environment(new FilesystemLoader($pathViews), [
            'debug' => $debug,
            'cache' => $cache
        ]);

        $app = new TwigFunction('app', function () {
            return app();
        });
        $route = new TwigFunction('route', function (string $uri) {
            return route($uri);
        });
        $assets = new TwigFunction('assets', function (string $pathFile) {
            return assets($pathFile);
        });
        $lang = new TwigFunction('__', function (string $keyName, array $values = []) {
            return __($keyName, $values);
        });

        $this->twigInstance->addFunction($app);
        $this->twigInstance->addFunction($route);
        $this->twigInstance->addFunction($assets);
        $this->twigInstance->addFunction($lang);
    }

    /**
     * @param string $pathViews
     * @param string|bool $cache
     * @param bool $debug
     * @return Twig
     */
    public static function getInstance(string $pathViews, string|bool $cache = false, bool $debug = false): Twig
    {
        if (!isset(self::$instance)) {
            self::$instance = new self($pathViews, $cache, $debug);
        }

        return self::$instance;
    }

    /**
     * @return Environment
     */
    public function getTwigInstance(): Environment
    {
        return $this->twigInstance;
    }

    /**
     * @return void
     */
    public function __clone(): void
    {
    }

    /**
     * @return void
     */
    public function __wakeup(): void
    {
    }
}