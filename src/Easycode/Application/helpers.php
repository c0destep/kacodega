<?php

declare(strict_types=1);

use Easycode\Application\EasyApp;
use Easycode\Http\Response;
use Easycode\Session\Session;

if (!function_exists('getClientIpServer')) {
    /**
     * Helper IP
     * @return string
     */
    function getClientIpServer(): string
    {
        if (isset($_SERVER['HTTP_CLIENT_IP'])) {
            $ipAddress = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ipAddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } elseif (isset($_SERVER['HTTP_X_FORWARDED'])) {
            $ipAddress = $_SERVER['HTTP_X_FORWARDED'];
        } elseif (isset($_SERVER['HTTP_FORWARDED_FOR'])) {
            $ipAddress = $_SERVER['HTTP_FORWARDED_FOR'];
        } elseif (isset($_SERVER['HTTP_FORWARDED'])) {
            $ipAddress = $_SERVER['HTTP_FORWARDED'];
        } elseif (isset($_SERVER['REMOTE_ADDR'])) {
            $ipAddress = $_SERVER['REMOTE_ADDR'];
        } else {
            $ipAddress = 'UNKNOWN';
        }

        return $ipAddress;
    }
}

if (!function_exists('detectBrowser')) {
    /**
     * Helper Detect your browser
     * @param string|null $userAgent
     * @param string|null $ip
     * @return array
     */
    function detectBrowser(string $userAgent = null, string $ip = null): array
    {
        if (is_null($ip)) {
            $ip = getClientIpServer();
        }

        if (is_null($userAgent)) {
            $userAgent = $_SERVER['HTTP_USER_AGENT'];
        }

        $browser = 'Unknown';
        $codename = 'Unknown';
        $platform = 'Unknown';

        if (preg_match('/linux/i', $userAgent)) {
            $platform = 'Linux';
        } elseif (preg_match('/macintosh|mac os x/i', $userAgent)) {
            $platform = 'MacOS';
        } elseif (preg_match('/windows|win32/i', $userAgent)) {
            $platform = 'Windows';
        }

        if (preg_match('/Edge/i', $userAgent)) {
            $browser = 'Microsoft Edge';
            $codename = 'Edge';
        } elseif (preg_match('/MSIE/i', $userAgent) && !preg_match('/Opera/i', $userAgent)) {
            $browser = 'Internet Explorer';
            $codename = 'MSIE';
        } elseif (preg_match('/Trident/i', $userAgent) && !preg_match('/Opera/i', $userAgent)) {
            $browser = 'Internet Explorer';
            $codename = 'Trident';
        } elseif (preg_match('/Firefox/i', $userAgent)) {
            $browser = 'Mozilla Firefox';
            $codename = 'Firefox';
        } elseif (preg_match('/Chrome/i', $userAgent)) {
            $browser = 'Google Chrome';
            $codename = 'Chrome';
        } elseif (preg_match('/AppleWebKit/i', $userAgent)) {
            $browser = 'AppleWebKit';
            $codename = 'Opera';
        } elseif (preg_match('/Safari/i', $userAgent)) {
            $browser = 'Apple Safari';
            $codename = 'Safari';
        } elseif (preg_match('/Netscape/i', $userAgent)) {
            $browser = 'Netscape';
            $codename = 'Netscape';
        }

        $known = ['Version', $codename, 'other'];
        $pattern = '#(?<browser>' . join('|', $known) . ')[/ ]+(?<version>[0-9.|a-zA-Z.]*)#';
        !preg_match_all($pattern, $userAgent, $matches);
        $i = count($matches['browser']);

        if ($i !== 1) {
            if (strripos($userAgent, 'Version') < strripos($userAgent, $codename)) {
                $version = $matches['version'][0];
            } else {
                $version = $matches['version'][1];
            }
        } else {
            $version = $matches['version'][0];
        }

        if ($codename === 'Trident') {
            preg_match('#rv:([\d.|a-zA-Z]*)#', $userAgent, $versions);
            $version = $versions[1];
        }

        return [
            'ip' => $ip,
            'userAgent' => $userAgent,
            'name' => $browser,
            'platform' => $platform,
            'pattern' => $pattern,
            'version' => $version
        ];
    }
}

if (!function_exists('route')) {
    /**
     * Helper Route
     * @param string $uri
     * @return string
     */
    function route(string $uri = ''): string
    {
        return app()->route($uri);
    }
}

if (!function_exists('assets')) {
    /**
     * Helper Assets Files
     * @param string $pathFile
     * @return string
     */
    function assets(string $pathFile): string
    {
        return app()->assets($pathFile);
    }
}

if (!function_exists('response')) {
    /**
     * Helper Response Class
     * @return Response
     */
    function response(): Response
    {
        return Response::getInstance();
    }
}

if (!function_exists('app')) {
    /**
     * Helper Application
     * @return EasyApp
     */
    function app(): EasyApp
    {
        return EasyApp::getInstance();
    }
}

if (!function_exists('__')) {
    /**
     * Helper Translation
     * @param string $keyName
     * @param array $values
     * @return string
     */
    function __(string $keyName, array $values = []): string
    {
        return app()->l($keyName, $values);
    }
}

if (!function_exists('env')) {
    /**
     * Helper Config
     * @param array|string $env
     * @return mixed
     */
    function env(array|string $env): mixed
    {
        return EasyApp::environment($env);
    }
}

if (!function_exists('setFlashError')) {
    /**
     * Assigning error message.
     * @param string $message
     */
    function setFlashError(string $message): void
    {
        Session::getInstance()->setFlash('error', $message);
    }
}

if (!function_exists('getFlashError')) {
    /**
     * If there is error message it shows.
     * @return string
     */
    function getFlashError(): string
    {
        return Session::getInstance()->getFlash('error') ?? '';
    }
}

if (!function_exists('setFlashSuccess')) {
    /**
     * Assigning success message.
     * @param string $message
     */
    function setFlashSuccess(string $message): void
    {
        Session::getInstance()->setFlash('success', $message);
    }
}

if (!function_exists('getFlashSuccess')) {
    /**
     * If there is success message it shows.
     * @return string
     */
    function getFlashSuccess(): string
    {
        return Session::getInstance()->getFlash('success') ?? '';
    }
}

if (!function_exists('setFlashWarning')) {
    /**
     * Assigning alert message.
     * @param string $message
     */
    function setFlashWarning(string $message): void
    {
        Session::getInstance()->setFlash('warning', $message);
    }
}

if (!function_exists('getFlashWarning')) {
    /**
     * If there is warning message it shows.
     * @return string
     */
    function getFlashWarning(): string
    {
        return Session::getInstance()->getFlash('warning') ?? '';
    }
}

if (!function_exists('setFlashInfo')) {
    /**
     * Assigning alert message.
     * @param string $message
     */
    function setFlashInfo(string $message): void
    {
        Session::getInstance()->setFlash('info', $message);
    }
}

if (!function_exists('getFlashInfo')) {
    /**
     * If there is info message it shows.
     * @return string
     */
    function getFlashInfo(): string
    {
        return Session::getInstance()->getFlash('info') ?? '';
    }
}
