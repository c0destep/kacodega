<?php

namespace Easycode\Session;

use ArrayIterator;
use Traversable;

class Session
{
    private static Session $instance;
    private string $sessionName;
    private int $sessionExpire = 120;

    private function __construct()
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_cache_expire($this->sessionExpire);
            session_name(uniqid('__easy'));
            session_start();
        }
    }

    public static function getInstance(): Session
    {
        if (!isset(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function setFlash(string $key, mixed $message): void
    {
        $Flash = $this->get('flash_data');

        if (isset($Flash[$key])) {
            unset($Flash[$key]);
        }

        $data = array_merge(is_array($Flash) ? $Flash : [], [$key => $message]);

        $this->set('flash_data', $data);
    }

    public function get(int|string $key, mixed $default = null): mixed
    {
        return $this->exists($key) ? $_SESSION[$key] : $default;
    }

    public function exists(int|string $key): bool
    {
        return array_key_exists($key, $_SESSION);
    }

    public function set(int|string $key, mixed $value): static
    {
        $_SESSION[$key] = $value;
        return $this;
    }

    public function getFlash(string $key): mixed
    {
        $flashData = $this->get('flash_data');
        $value = $flashData[$key] ?? null;

        if (isset($flashData[$key])) {
            unset($flashData[$key]);
            $this->set('flash_data', $flashData);
        }

        return $value;
    }

    public function merge(int|string $key, mixed $value): static
    {
        if (is_array($value) && is_array($old = $this->get($key))) {
            $value = array_merge_recursive($old, $value);
        }
        return $this->set($key, $value);
    }

    public function clear(): static
    {
        $_SESSION = [];
        return $this;
    }

    public function __get(int|string $key)
    {
        return $this->get($key);
    }

    public function __set(int|string $key, mixed $value)
    {
        $this->set($key, $value);
    }

    public function __unset(int|string $key)
    {
        $this->delete($key);
    }

    public function delete(int|string $key): static
    {
        if ($this->exists($key)) {
            unset($_SESSION[$key]);
        }
        return $this;
    }

    public function __isset(int|string $key)
    {
        return $this->exists($key);
    }

    public function count(): int
    {
        return count($_SESSION);
    }

    public function getIterator(): Traversable|ArrayIterator
    {
        return new ArrayIterator($_SESSION);
    }

    public function offsetExists(mixed $offset): bool
    {
        return $this->exists($offset);
    }

    public function offsetGet(mixed $offset): mixed
    {
        return $this->get($offset);
    }

    public function offsetSet(mixed $offset, mixed $value): void
    {
        $this->set($offset, $value);
    }

    public function offsetUnset(mixed $offset): void
    {
        $this->delete($offset);
    }

    public function __clone(): void
    {
    }

    public function __wakeup(): void
    {

    }

    private function destroy(): void
    {
        if ($this->generateId()) {
            session_unset();
            session_destroy();
            session_write_close();
            if (ini_get('session.use_cookies')) {
                $parameters = session_get_cookie_params();
                setcookie(
                    session_name(),
                    '',
                    time() - 4200,
                    $parameters['path'],
                    $parameters['domain'],
                    $parameters['secure'],
                    $parameters['httponly']
                );
            }
        }
    }

    private function generateId(bool $new = false): bool|string
    {
        if ($new && session_id()) {
            session_regenerate_id(true);
        }
        return session_id() ?: '';
    }
}