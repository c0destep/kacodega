<?php

declare(strict_types=1);

namespace Easycode\View;

use Twig\Environment;
use Twig\Loader\FilesystemLoader;

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