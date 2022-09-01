<?php

declare(strict_types=1);

namespace Easycode\Encryption;

class Hash
{
    /**
     * @var int
     */
    protected static int $cost = 12;

    /**
     *
     */
    private function __construct()
    {
    }

    /**
     * @param string $password
     * @return string
     */
    public static function encryptPassword(string $password): string
    {
        if ($password === '') {
            return $password;
        }

        return password_hash($password, PASSWORD_BCRYPT, [
            'cost' => self::$cost
        ]);
    }

    /**
     * @return int
     */
    public static function costCheck(): int
    {
        $timeTarget = 0.75;
        $cost = 10;

        do {
            $cost++;
            $start = microtime(true);
            password_hash('Hj6%G46s1@3', PASSWORD_BCRYPT, [
                'cost' => $cost
            ]);
            $end = microtime(true);
        } while ($end - $start < $timeTarget);

        return $cost;
    }

    /**
     * @param string $password
     * @param string $encryptedPassword
     * @return bool
     */
    public static function verifyPassword(string $password, string $encryptedPassword): bool
    {
        if (empty($password) || empty($encryptedPassword)) {
            return false;
        }

        return password_verify($password, $encryptedPassword);
    }
}