<?php

declare(strict_types=1);

namespace Easycode\Http;

use Easycode\Routing\Controller;
use Easycode\View\Twig;
use Easycode\View\View;
use Easycode\View\ViewHtml;
use Easycode\View\ViewJson;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class Response
{
    /**
     * @var Response
     */
    private static Response $instance;
    /**
     * @var Controller
     */
    protected Controller $controller;
    /**
     * @var array
     */
    protected array $header = [];

    /**
     *
     */
    private function __construct()
    {
    }

    /**
     * @param string|null $key
     * @return array|string
     */
    public function getHeader(string $key = null): array|string
    {
        if (is_null($key)) {
            return $this->header;
        }

        return $this->header[$key] ?? '';
    }

    /**
     * Set Headers on response
     *
     * @param string[]|string $header
     * @param string|null $value
     * @return void
     */
    public function setHeader(array|string $header, string $value = null): void
    {
        if (is_array($header)) {
            $this->header = $header;
        }

        if (is_string($header)) {
            $this->header[$header] = $value ?? '';
        }
    }

    /**
     * @return ViewHtml
     */
    public function html(): ViewHtml
    {
        $this->setHeader('Content-Type', ContentType::CONTENT_HTML->value);
        return new ViewHtml();
    }

    /**
     * @return ViewJson
     */
    public function json(): ViewJson
    {
        $this->setHeader('Content-Type', ContentType::CONTENT_JSON->value);
        return new ViewJson();
    }

    /**
     * @param ViewHtml|ViewJson $view
     * @return void
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    public function renderView(ViewHtml|ViewJson $view): void
    {
        if ($view->getType() === View::HTML) {
            echo Twig::getInstance(app()->getViewPath(), app()->getCachePath(), true)->getTwigInstance()->render($view->getView(), $view->getParameters());
        }

        echo $view->toJson();
    }

    /**
     * @return Response
     */
    public static function getInstance(): Response
    {
        if (!isset(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * @return Controller
     */
    public function getController(): Controller
    {
        return $this->controller;
    }

    /**
     * @param Controller $controller
     * @return void
     */
    public function setController(Controller $controller): void
    {
        $this->controller = $controller;
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