<?php

namespace Easycode\Translation;

class Lang
{
    private static Lang $instance;

    /**
     * @param string $directory
     * @param string $current
     * @param string $default
     * @param array $available
     * @param string $nameCookie
     * @param int $validateCookie
     */
    private function __construct(private string $directory, private string $current, private string $default, private array $available = [], private string $nameCookie = 'lang', private int $validateCookie = 86400 * 24 * 7)
    {
        $this->directory = $this->secure($directory);
        $this->current = $this->secure($current);
        $this->default = $this->secure($default);
        $this->available = sizeof($available) === 0 ? [$this->current] : $available;
        $this->nameCookie = $this->secure($nameCookie);
        $this->validateCookie = time() + $validateCookie;
        $this->setCookie();
    }

    /**
     * @param string $value
     * @return string
     */
    private function secure(string $value): string
    {
        return htmlspecialchars(trim($value));
    }

    /**
     * @return void
     */
    private function setCookie(): void
    {
        setcookie($this->nameCookie, $this->current, $this->validateCookie);
    }

    /**
     * @param string $directory
     * @param string $current
     * @param string $default
     * @param array $available
     * @param string $nameCookie
     * @param int $validateCookie
     * @return Lang
     */
    public static function getInstance(string $directory, string $current, string $default, array $available = [], string $nameCookie = 'lang', int $validateCookie = 86400 * 24 * 7): Lang
    {
        if (!isset(self::$instance)) {
            self::$instance = new self($directory, $current, $default, $available, $nameCookie, $validateCookie);
        }

        return self::$instance;
    }

    /**
     * @return string
     */
    public function getDefault(): string
    {
        return $this->default;
    }

    /**
     * @param string $default
     * @return Lang
     */
    public function setDefault(string $default): Lang
    {
        $this->default = $default;
        return $this;
    }

    /**
     * @return array
     */
    public function getAvailable(): array
    {
        return $this->available;
    }

    /**
     * @param array|string $available
     * @return Lang
     */
    public function setAvailable(array|string $available): Lang
    {
        if (is_array($available)) {
            $this->available = $available;
        }

        if (!array_key_exists($available, $this->available)) {
            $this->available[] = $available;
        }

        return $this;
    }

    /**
     * @param string $keyName
     * @param array $values
     * @return mixed
     */
    public function key(string $keyName, array $values = []): mixed
    {
        $language = $this->getContentFile();

        if (strpos($keyName, '@')) {
            preg_match_all('/([a-zA-Z0-9_]+)/', $keyName, $matches);

            foreach ($matches[0] as $index => $match) {
                if ($index === 0) {
                    $keyName = $language[$match][0];
                } else {
                    if (array_key_exists(0, $keyName)) {
                        $keyName = $keyName[0][$match];
                    } else {
                        $keyName = $keyName[$match];
                    }
                }
            }

            if (sizeof($values) > 0) {
                return $this->make($keyName, $values);
            }

            return $keyName;
        }

        if (sizeof($values) > 0) {
            return $this->make($language[$keyName], $values);
        }

        return $language[$keyName] ?? $keyName;
    }

    /**
     * @return array
     */
    public function getContentFile(): array
    {
        if (in_array($this->current, $this->available)) {
            return json_decode(file_get_contents($this->directory . '/' . $this->current . '.json'), true) ?? [];
        }

        return json_decode(file_get_contents($this->directory . '/' . $this->default . '.json'), true) ?? [];
    }

    /**
     * @param string $language
     * @param array $values
     * @return array|string
     */
    private function make(string $language, array $values): array|string
    {
        preg_match_all('/(:[_a-zA-Z0-9]+)/', $language, $matches);

        foreach ($matches[0] as $match) {
            $match = str_replace(':', '', $match);

            if (array_key_exists($match, $values)) {
                $language = str_replace(':' . $match, $values[$match], $language);
            }
        }

        return preg_replace('/::/', ':', $language, 1) ?? [];
    }

    /**
     * @return string
     */
    public function getDirectory(): string
    {
        return $this->directory;
    }

    /**
     * @param string $directory
     * @return Lang
     */
    public function setDirectory(string $directory): Lang
    {
        $this->directory = $directory;
        return $this;
    }

    /**
     * @return string
     */
    public function getNameCookie(): string
    {
        return $this->nameCookie;
    }

    /**
     * @param string $nameCookie
     * @return Lang
     */
    public function setNameCookie(string $nameCookie): Lang
    {
        $this->nameCookie = $nameCookie;
        return $this;
    }

    /**
     * @return int
     */
    public function getValidateCookie(): int
    {
        return $this->validateCookie;
    }

    /**
     * @param int $validateCookie
     * @return Lang
     */
    public function setValidateCookie(int $validateCookie): Lang
    {
        $this->validateCookie = $validateCookie;
        return $this;
    }

    /**
     * @return string
     */
    public function getLanguage(): string
    {
        return $this->current;
    }

    /**
     * @param string $language
     * @return Lang
     */
    public function setLanguage(string $language): Lang
    {
        $this->current = $language;
        return $this;
    }

    public function __clone(): void
    {
    }

    public function __wakeup(): void
    {
    }
}