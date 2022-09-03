<?php

declare(strict_types=1);

namespace Easycode\Application;

use Dotenv\Dotenv;
use Easycode\Database\Eloquent;
use Easycode\Http\Method;
use Easycode\Http\Request;
use Easycode\Http\Response;
use Easycode\Routing\Route;
use Easycode\Translation\Lang;
use Easycode\View\ViewHtml;
use Easycode\View\ViewJson;
use ReflectionClass;
use ReflectionException;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class EasyApp
{
    /**
     * The Easycode framework version.
     *
     * @var string
     */
    public const VERSION = '1.0.0-beta.1';
    /**
     * @var EasyApp
     */
    private static EasyApp $instance;
    /**
     * @var array
     */
    private static array $environments;
    /**
     * @var string
     */
    protected string $rootPath;
    /**
     * @var string
     */
    protected string $basePath;
    /**
     * @var string
     */
    protected string $cachePath;
    /**
     * @var string
     */
    protected string $modelPath;
    /**
     * @var string
     */
    protected string $viewPath;
    /**
     * @var string
     */
    protected string $routePath;
    /**
     * @var string
     */
    protected string $storagePath;
    /**
     * @var string
     */
    protected string $langPath;
    /**
     * @var string
     */
    protected string $baseRoute;
    /**
     * @var string
     */
    protected string $requestMethod;
    /**
     * @var string
     */
    protected string $requestUri;
    /**
     * @var array
     */
    protected array $patchUri = [];
    /**
     * @var array
     */
    protected array $route = [];
    /**
     * @var array
     */
    protected array $parameters = [];

    /**
     * @param string|null $path
     */
    private function __construct(string $path = null)
    {
        self::$instance = $this;

        if (is_null($path)) {
            $path = $_SERVER['PHP_SELF'];
        }

        $this->rootPath = dirname(str_replace('\\', '/', trim($path)));
        $this->basePath = $this->rootPath . '/app';
        $this->modelPath = $this->basePath . '/Models';
        $this->cachePath = $this->rootPath . '/cache/template';
        $this->viewPath = $this->rootPath . '/views';
        $this->routePath = $this->rootPath . '/routes';
        $this->storagePath = $this->rootPath . '/storage';
        $this->langPath = $this->rootPath . '/lang';

        $dotenv = Dotenv::createImmutable($this->rootPath);
        $dotenv->load();

        $dotenv->required('APP_ENV')->allowedValues(['development', 'production']);

        $dotenv->required('DEFAULT_LANGUAGE')->notEmpty();
        $dotenv->ifPresent('AVAILABLE_LANGUAGES')->notEmpty();

        $dotenv->required('DB_ACTIVE')->isBoolean();
        $dotenv->ifPresent('DB_DRIVER')->notEmpty();
        $dotenv->ifPresent('DB_HOST')->notEmpty();
        $dotenv->ifPresent('DB_NAME')->notEmpty();
        $dotenv->ifPresent('DB_USER')->notEmpty();

        self::$environments = $_ENV;

        switch (self::environment('APP_ENV')) {
            case 'development':
                ini_set('display_errors', true);
                error_reporting(-1);
                break;
            case 'production':
                ini_set('display_errors', false);
                error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED & ~E_STRICT & ~E_USER_NOTICE & ~E_USER_DEPRECATED);
                break;
            default:
                header('HTTP/1.1 503 Service Unavailable.', true, 503);
                echo 'The application environment is not set correctly.';
                exit(503);
        }

        $this->loadRoutes();

        $pathDirectoryServer = $_SERVER['DOCUMENT_ROOT'];
        $pathDirectorySystem = substr($this->rootPath . '/public/', strlen($pathDirectoryServer));
        $directoryApp = substr($pathDirectorySystem, 0, strlen($pathDirectorySystem));
        $protocol = isset($_SERVER['HTTPS']) && filter_var($_SERVER['HTTPS'], FILTER_VALIDATE_BOOLEAN) ? 'https://' : 'http://';

        $this->baseRoute = $protocol . $_SERVER['SERVER_NAME'] . $directoryApp;

        if (self::environment('DB_ACTIVE')) {
            $eloquent = new Eloquent();
            $eloquent->createConnection();
        }

        $this->requestUri = str_replace([$_SERVER['QUERY_STRING'], '?', $directoryApp], '', $_SERVER['REQUEST_URI']);
        $this->requestMethod = $_SERVER['REQUEST_METHOD'];
        $this->patchUri = explode(DIRECTORY_SEPARATOR, $this->requestUri);

        if (empty($this->requestUri)) {
            $this->requestUri = '/';
            $this->patchUri = [$this->requestUri];
        }

        if (!Route::checkRoute(Method::from($this->requestMethod), $this->requestUri)) {
            die('Route not found');
        }

        $this->route = Route::getInstance()->getRoute(Method::from($this->requestMethod), $this->requestUri);

        Route::validateRoute($this->route);
        Route::clearRoutes();

        $this->callbacks($this->route, 'onCallBefore', $this->route['parameters'] ?? []);
        if ($this->run($this->route['controller'], $this->route['action'], $this->route['parameters'] ?? [])) {
            $this->callbacks($this->route, 'onCallAfter', $this->route['parameters'] ?? []);
        }
    }

    /**
     * @param string|array $env
     * @return string|array|null
     */
    public static function environment(string|array $env): string|array|null
    {
        if (is_array($env)) {
            $temp = [];

            foreach ($env as $key) {
                if (array_key_exists($key, self::$environments)) {
                    $temp[] = self::$environments[$key];
                }
            }

            return $temp;
        }

        if (array_key_exists($env, self::$environments)) {
            return self::$environments[$env];
        }

        return null;
    }

    /**
     * @return void
     */
    private function loadRoutes(): void
    {
        if (file_exists($this->rootPath . '/routes/web.php')) {
            include $this->rootPath . '/routes/web.php';
        }
    }

    /**
     * @param string|null $path
     * @return EasyApp
     */
    public static function getInstance(string $path = null): EasyApp
    {
        if (!isset(self::$instance)) {
            self::$instance = new self($path);
        }

        return self::$instance;
    }

    /**
     * @param array $callbacks
     * @param string $type
     * @param array $attrs
     * @return void
     */
    private function callbacks(array $callbacks, string $type, array $attrs = []): void
    {
        if (array_key_exists($type, $callbacks) && is_array($callbacks[$type])) {
            foreach ($callbacks[$type] as $settings) {
                $class = $settings[0];
                $method = $settings[1];

                try {
                    $verifyClass = new ReflectionClass($class);
                    $parameters = $verifyClass->getMethod($method)->getParameters();
                    $classInstance = new $class();

                    $nAttrs = [];
                    foreach ($parameters as $parameter) {
                        $nameParameter = $parameter->getName();
                        if (isset($attrs[$nameParameter])) {
                            $nAttrs[] = $attrs[$nameParameter];
                        }
                    }

                    call_user_func_array([$classInstance, $method], $nAttrs);
                } catch (ReflectionException $exception) {
                    die($exception->getMessage());
                }
            }
        }
    }

    /**
     * @param string $class
     * @param string $method
     * @param array $parameters
     * @return bool
     */
    private function run(string $class, string $method, array $parameters = []): bool
    {
        if (class_exists($class)) {
            try {
                $verifyClass = new ReflectionClass($class);
                $classParameters = $verifyClass->getMethod($method)->getParameters();
                $classInstance = new $class();

                $nParameters = [];
                foreach ($classParameters as $parameter) {
                    $nameParameter = $parameter->getName();

                    $nParameters[] = Request::getInstance();

                    if (isset($parameters[$nameParameter])) {
                        $nParameters[] = $parameters[$nameParameter];
                    }

                    Response::getInstance()->setController($classInstance);
                }

                $returnFunction = call_user_func_array([$classInstance, $method], $nParameters);
                if (($returnFunction instanceof ViewHtml) || ($returnFunction instanceof ViewJson)) {
                    Response::getInstance()->renderView($returnFunction);
                }

                return true;
            } catch (ReflectionException $reflectionException) {
                die($reflectionException->getMessage());
            } catch (LoaderError|RuntimeError|SyntaxError $e) {
                die($e->getMessage());
            }
        }

        return false;
    }

    /**
     * @param string $keyName
     * @param array $values
     * @return string
     */
    public function l(string $keyName, array $values = []): string
    {
        return Lang::getInstance($this->langPath, $this->getClientLanguage(), self::environment('DEFAULT_LANGUAGE'), explode(',', self::environment('AVAILABLE_LANGUAGES')))->key($keyName, $values);
    }

    /**
     * @return string
     */
    public function getClientLanguage(): string
    {
        $httpAccept = str_replace('-', '_', getenv('HTTP_ACCEPT_LANGUAGE'));

        if (strlen($httpAccept) > 1) {

            $x = explode(',', $httpAccept);
            $lang = [];

            foreach ($x as $val) {
                if (preg_match('/(.*);q=([0-1]?.d{0,4})/i', $val, $matches)) {
                    $lang[$matches[1]] = (float)$matches[2];
                } else {
                    $lang[$val] = 1.0;
                }
            }

            $val = 0.0;
            foreach ($lang as $key => $value) {
                if ($value > $val) {
                    return $key;
                }
            }
        }

        return self::environment('DEFAULT_LANGUAGE');
    }

    /**
     * @return string
     */
    public function getVersion(): string
    {
        return self::VERSION;
    }

    /**
     * @param string $pathFile
     * @return string
     */
    public function assets(string $pathFile): string
    {
        return $this->route($pathFile);
    }

    /**
     * @param string $uri
     * @return string
     */
    public function route(string $uri): string
    {
        return str_replace('//', '/', $this->baseRoute . trim($uri));
    }

    /**
     * @return string
     */
    public function getCachePath(): string
    {
        return $this->cachePath;
    }

    /**
     * @return string
     */
    public function getViewPath(): string
    {
        return $this->viewPath;
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