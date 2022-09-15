<?php

declare(strict_types=1);

namespace Easycode\View;

use Easycode\Application\EasyApp;
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

        $app = new TwigFunction('app', function (): EasyApp {
            return app();
        });
        $env = new TwigFunction('env', function (array|string $env): array|string|null {
            return env($env);
        });
        $route = new TwigFunction('route', function (string $uri = ''): string {
            return route($uri);
        });
        $assets = new TwigFunction('assets', function (string $pathFile): string {
            return assets($pathFile);
        });
        $lang = new TwigFunction('__', function (string $keyName, array $values = []): string {
            return __($keyName, $values);
        });
        $getFlashSuccess = new TwigFunction('getFlashSuccess', function (): string {
            return getFlashSuccess();
        });
        $getFlashError = new TwigFunction('getFlashError', function (): string {
            return getFlashError();
        });
        $getFlashWarning = new TwigFunction('getFlashWarning', function (): string {
            return getFlashWarning();
        });
        $getFlashInfo = new TwigFunction('getFlashInfo', function (): string {
            return getFlashInfo();
        });

        $this->twigInstance->addFunction($app);
        $this->twigInstance->addFunction($env);
        $this->twigInstance->addFunction($route);
        $this->twigInstance->addFunction($assets);
        $this->twigInstance->addFunction($lang);
        $this->twigInstance->addFunction($getFlashSuccess);
        $this->twigInstance->addFunction($getFlashError);
        $this->twigInstance->addFunction($getFlashWarning);
        $this->twigInstance->addFunction($getFlashInfo);
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