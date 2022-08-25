<?php

declare(strict_types=1);

namespace Easycode\Routing;

use Easycode\Http\Method;
use Easycode\Http\Response;
use http\Exception\InvalidArgumentException;

class Route
{
    /**
     * @var array
     */
    protected static array $basicRoutes = [];
    /**
     * @var array
     */
    protected static array $dynamicRoutes = [];
    /**
     * @var Route
     */
    private static Route $instance;

    /**
     *
     */
    private function __construct()
    {
    }

    /**
     * @param string $uri
     * @param array $action
     * @param array $headers
     * @param array $options
     * @return void
     */
    public static function all(string $uri, array $action, array $headers = [], array $options = []): void
    {
        self::addRoute([Method::GET, Method::POST, Method::PUT, Method::DELETE, Method::HEAD], $uri, $action[0], $action[1], $headers, $options);
    }

    /**
     * @param Method[] $methods
     * @param string $uri
     * @param string $controller
     * @param string $action
     * @param array $headers
     * @param array $options
     * @return void
     */
    public static function addRoute(array|Method $methods, string $uri, string $controller, string $action, array $headers = [], array $options = []): void
    {
        if (is_array($methods)) {
            foreach ($methods as $method) {
                self::setRoute($method, $uri, [
                    'controller' => $controller,
                    'action' => $action,
                    'headers' => $headers,
                    'options' => $options
                ]);
            }
        } else {
            self::setRoute($methods, $uri, [
                'controller' => $controller,
                'action' => $action,
                'headers' => $headers,
                'options' => $options
            ]);
        }
    }

    /**
     * @param Method $method
     * @param string $uri
     * @param array $settings
     * @return void
     */
    public static function setRoute(Method $method, string $uri, array $settings = []): void
    {
        preg_match_all('/{(.*?)}/', $uri, $matches);

        if (count($matches[0]) > 0) {
            self::$dynamicRoutes[$method->value][$uri] = $settings;
        } else {
            self::$basicRoutes[$method->value][$uri] = $settings;
        }
    }

    /**
     * @param string $uri
     * @param array $action
     * @param array $headers
     * @param array $options
     * @return void
     */
    public static function get(string $uri, array $action, array $headers = [], array $options = []): void
    {
        self::addRoute([Method::GET, Method::HEAD], $uri, $action[0], $action[1], $headers, $options);
    }

    /**
     * @param string $uri
     * @param array $action
     * @param array $headers
     * @param array $options
     * @return void
     */
    public static function post(string $uri, array $action, array $headers = [], array $options = []): void
    {
        self::addRoute(Method::POST, $uri, $action[0], $action[1], $headers, $options);
    }

    /**
     * @param string $uri
     * @param array $action
     * @param array $headers
     * @param array $options
     * @return void
     */
    public static function put(string $uri, array $action, array $headers = [], array $options = []): void
    {
        self::addRoute(Method::PUT, $uri, $action[0], $action[1], $headers, $options);
    }

    /**
     * @param string $uri
     * @param array $action
     * @param array $headers
     * @param array $options
     * @return void
     */
    public static function delete(string $uri, array $action, array $headers = [], array $options = []): void
    {
        self::addRoute(Method::DELETE, $uri, $action[0], $action[1], $headers, $options);
    }

    /**
     * @param string $uri
     * @param array $action
     * @param array $headers
     * @param array $options
     * @return void
     */
    public static function head(string $uri, array $action, array $headers = [], array $options = []): void
    {
        self::addRoute(Method::HEAD, $uri, $action[0], $action[1], $headers, $options);
    }

    /**
     * @param string $uri
     * @param array $action
     * @param array $headers
     * @param array $options
     * @return void
     */
    public static function options(string $uri, array $action, array $headers = [], array $options = []): void
    {
        self::addRoute(Method::OPTIONS, $uri, $action[0], $action[1], $headers, $options);
    }

    /**
     * @param string $uri
     * @param array $action
     * @param array $headers
     * @param array $options
     * @return void
     */
    public static function patch(string $uri, array $action, array $headers = [], array $options = []): void
    {
        self::addRoute(Method::PATCH, $uri, $action[0], $action[1], $headers, $options);
    }

    /**
     * @param Method $method
     * @param string $prefix
     * @param array $routes
     * @return void
     */
    public static function group(Method $method, string $prefix, array $routes): void
    {
        $prefix = $prefix === '' ? '/' : $prefix;

        foreach ($routes as $uri => $settings) {
            if ($uri === '') {
                $uri = $prefix;
            } else {
                $uri = $prefix . '/' . $uri;
            }

            $uri = str_replace('//', '/', $uri);

            if (sizeof($settings) === 0) {
                throw new InvalidArgumentException("route $uri settings not found");
            } elseif ($settings[0][0] === '') {
                throw new InvalidArgumentException("route $uri controller not found");
            } elseif ($settings[0][1] === '') {
                throw new InvalidArgumentException("route $uri action not found");
            } else {
                self::addRoute($method, $uri, $settings[0][0], $settings[0][1], $settings['headers'] ?? [], $settings['options'] ?? []);
            }
        }
    }

    /**
     * @param Method $method
     * @param string $uri
     * @return bool
     */
    public static function checkRoute(Method $method, string $uri): bool
    {
        if (isset(self::$basicRoutes[$method->value][$uri])) {
            return true;
        } elseif (isset(self::$dynamicRoutes[$method->value])) {
            foreach (self::$dynamicRoutes[$method->value] as $index => $settings) {
                preg_match_all("/{(.*?)}/", $index, $variables);
                $key = str_replace($variables[0], '([^/]+)', $index);

                if (preg_match('#^' . $key . '$#', $uri, $matches)) {
                    $data = [];

                    foreach ($variables[1] as $k => $variable) {
                        $data[$variable] = $matches[$k + 1];
                    }

                    $settings['params'] = $data;
                    self::$basicRoutes[$method->value][$matches[0]] = $settings;

                    return true;
                }
            }
        }

        return false;
    }

    /**
     * @param array $settings
     * @return void
     */
    public static function validateRoute(array $settings): void
    {
        if (isset($settings[1]) && is_array($settings[1])) {
            Response::getInstance()->setHeader($settings[1]);
        }
    }

    /**
     * @return Route
     */
    public static function getInstance(): Route
    {
        if (!isset(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * @return void
     */
    public static function clearRoutes(): void
    {
        self::$basicRoutes = [];
        self::$dynamicRoutes = [];
    }

    /**
     * @param Method $method
     * @param string $uri
     * @return array
     */
    public function getRoute(Method $method, string $uri): array
    {
        return self::$basicRoutes[$method->value][$uri] ?? [];
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