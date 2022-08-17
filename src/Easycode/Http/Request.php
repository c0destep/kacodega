<?php

declare(strict_types=1);

namespace Easycode\Http;

class Request
{
    /**
     * @var Request
     */
    private static Request $instance;
    /**
     * @var array
     */
    protected readonly array $header;

    /**
     *
     */
    private function __construct()
    {
        $this->header = $this->getHeadersRequest();
    }

    /**
     * @return array
     */
    private function getHeadersRequest(): array
    {
        $header = [];
        $nHeader = [
            'CONTENT_TYPE' => 'Content-Type',
            'CONTENT_LENGTH' => 'Content-Length',
            'CONTENT_MD5' => 'Content-Md5',
        ];

        foreach ($_SERVER as $key => $value) {
            if (str_starts_with($key, 'HTTP_')) {
                $key = substr($key, 5);
                if (!isset($nHeader[$key]) || !isset($_SERVER[$key])) {
                    $key = str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', $key))));
                    $header[$key] = $value;
                }
            } elseif (isset($nHeader[$key])) {
                $header[$nHeader[$key]] = $value;
            }
        }

        if (!isset($header['Authorization'])) {
            if (isset($_SERVER['REDIRECT_HTTP_AUTHORIZATION'])) {
                $header['Authorization'] = $_SERVER['REDIRECT_HTTP_AUTHORIZATION'];
            } elseif (isset($_SERVER['PHP_AUTH_USER'])) {
                $basicPass = $_SERVER['PHP_AUTH_PW'] ?? '';
                $header['Authorization'] = 'Basic ' . base64_encode($_SERVER['PHP_AUTH_USER'] . ':' . $basicPass);
            } elseif (isset($_SERVER['PHP_AUTH_DIGEST'])) {
                $header['Authorization'] = $_SERVER['PHP_AUTH_DIGEST'];
            }
        }

        return $header;
    }

    /**
     * @return Request
     */
    public static function getInstance(): Request
    {
        if (!isset(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * @param array|null $only
     * @return array
     */
    public function all(array $only = null): array
    {
        $method = $this->getMethodRequest();

        if ($method === Method::GET) {
            $data = filter_var_array($_GET, FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?? [];

            if (is_null($only)) {
                return $data;
            }

            return array_filter($data, fn($key) => in_array($key, $only), ARRAY_FILTER_USE_KEY);
        } elseif ($method === Method::POST) {
            $data = filter_var_array($_POST, FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?? [];

            if (is_null($only)) {
                return $data;
            }

            return array_filter($data, fn($key) => in_array($key, $only), ARRAY_FILTER_USE_KEY);
        } elseif ($method === Method::PUT) {
            $data = $this->getInputMethodPut() ?? [];

            if (is_null($only)) {
                return $data;
            }

            return array_filter($data, fn($key) => in_array($key, $only), ARRAY_FILTER_USE_KEY);
        } elseif ($method === Method::DELETE) {
            $data = $this->getInputMethodDelete() ?? [];

            if (is_null($only)) {
                return $data;
            }

            return array_filter($data, fn($key) => in_array($key, $only), ARRAY_FILTER_USE_KEY);
        } else {
            return [];
        }
    }

    /**
     * @return Method|null
     */
    private function getMethodRequest(): ?Method
    {
        return Method::tryFrom(filter_input(INPUT_SERVER, 'REQUEST_METHOD', FILTER_SANITIZE_ENCODED));
    }

    /**
     * @param string|null $input
     * @param mixed|null $default
     * @return mixed
     */
    private function getInputMethodPut(string $input = null, mixed $default = null): mixed
    {
        parse_str(file_get_contents('php://input', false, null, -1, $_SERVER['CONTENT_LENGTH']), $_PUT);
        $_PUT = filter_var_array($_PUT, FILTER_SANITIZE_FULL_SPECIAL_CHARS);

        if (is_null($input)) {
            return $_PUT;
        }

        return $_PUT[$input] ?? $default;
    }

    /**
     * @param string|null $input
     * @param mixed|null $default
     * @return mixed
     */
    private function getInputMethodDelete(string $input = null, mixed $default = null): mixed
    {
        parse_str(file_get_contents('php://input', false, null, -1, $_SERVER['CONTENT_LENGTH']), $_DELETE);
        $_DELETE = filter_var_array($_DELETE, FILTER_SANITIZE_FULL_SPECIAL_CHARS);

        if (is_null($input)) {
            return $_DELETE;
        }

        return $_DELETE[$input] ?? $default;
    }

    /**
     * @param string $input
     * @param mixed|null $default
     * @return mixed
     */
    public function input(string $input, mixed $default = null): mixed
    {
        return match ($this->getMethodRequest()) {
            Method::GET => filter_input(INPUT_GET, $input, FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?? $default,
            Method::POST => filter_input(INPUT_POST, $input, FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?? $default,
            Method::PUT => $this->getInputMethodPut($input, $default),
            Method::DELETE => $this->getInputMethodDelete($input, $default),
            default => $default
        };
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