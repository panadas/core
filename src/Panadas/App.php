<?php
namespace Panadas;

class App extends \Panadas\BaseAbstract
{

    private $name;
    private $loader;
    private $service_container;
    private $master_request;
    private $active_request;
    private $server_vars = [];
    private $env_vars = [];
    private $events = [];

    const ACTION_EXCEPTION = "Exception";
    const ACTION_HTTP_ERROR = "HttpError";
    const ACTION_REDIRECT = "Redirect";

    public function __construct($name, \Panadas\Loader $loader, callable $service_container_callback, array $server_vars, array $env_vars)
    {
        parent::__construct();

        $this
            ->setName($name)
            ->replaceServerVars($server_vars)
            ->replaceEnvVars($env_vars)
            ->setLoader($loader)
            ->setServiceContainer($service_container_callback($this));

        if ($this->isDebugMode()) {
            ini_set("display_errors", true);
            error_reporting(-1);
        } else {
            ini_set("display_errors", false);
            error_reporting(E_ALL & ~E_DEPRECATED & ~E_STRICT);
        }

        if (ini_get("date.timezone") == "") {
            date_default_timezone_set("UTC");
        }

        set_error_handler([$this, "errorHandler"]);
        set_exception_handler([$this, "exceptionHandler"]);
    }

    public function __toArray()
    {
        return (
            parent::__toArray()
            + [
                "debug_mode" => $this->isDebugMode(),
                "name" => $this->getName(),
                "server_vars" => $this->getAllServerVars(),
                "env_vars" => $this->getAllEnvVars()
            ]
        );
    }

    public function getName()
    {
        return $this->name;
    }

    protected function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    public function getLoader()
    {
        return $this->loader;
    }

    protected function setLoader(\Panadas\Loader $loader)
    {
        $this->loader = $loader;

        return $this;
    }

    protected function getMasterRequest()
    {
        return $this->master_request;
    }

    protected function hasMasterRequest()
    {
        return !is_null($this->getMasterRequest());
    }

    protected function setMasterRequest(\Panadas\Request $master_request = null)
    {
        $this->master_request = $master_request;

        return $this;
    }

    protected function removeMasterRequest()
    {
        return $this->setMasterRequest(null);
    }

    protected function getActiveRequest()
    {
        return $this->active_request;
    }

    protected function hasActiveRequest()
    {
        return !is_null($this->getActiveRequest());
    }

    protected function setActiveRequest(\Panadas\Request $active_request = null)
    {
        $this->active_request = $active_request;

        return $this;
    }

    protected function removeActiveRequest()
    {
        return $this->setMasterRequest(null);
    }

    public function getServiceContainer()
    {
        return $this->service_container;
    }

    protected function setServiceContainer(\Panadas\ServiceContainer $service_container)
    {
        $this->service_container = $service_container;

        return $this;
    }

    public function getEnvVar($name, $default = null)
    {
        return $this->hasEnvVar($name) ? $this->env_vars[$name] : $default;
    }

    public function getAllEnvVars()
    {
        return $this->env_vars;
    }

    public function getEnvVarNames()
    {
        return array_keys($this->getAllEnvVars());
    }

    public function hasEnvVar($name)
    {
        return array_key_exists($name, $this->getAllEnvVars());
    }

    public function hasAnyEnvVars()
    {
        return (count($this->getAllEnvVars()) > 0);
    }

    public function removeEnvVar($name)
    {
        if ($this->hasEnvVar($name)) {
            unset($this->env_vars[$name]);
        }

        return $this;
    }

    public function removeManyEnvVars(array $names)
    {
        foreach ($names as $name) {
            $this->removeEnvVar($name);
        }

        return $this;
    }

    public function removeAllEnvVars()
    {
        return $this->removeManyEnvVars($this->getEnvVarNames());
    }

    public function replaceEnvVars(array $env_vars)
    {
        return $this
            ->removeAllEnvVars()
            ->setManyEnvVars($env_vars);
    }

    public function setEnvVar($name, $value)
    {
        $this->env_vars[$name] = $value;
        return $this;
    }

    public function setManyEnvVars(array $env_vars)
    {
        foreach ($env_vars as $name => $value) {
            $this->setEnvVar($name, $value);
        }

        return $this;
    }

    public function getServerVar($name, $default = null)
    {
        return $this->hasServerVar($name) ? $this->server_vars[$name] : $default;
    }

    public function getAllServerVars()
    {
        return $this->server_vars;
    }

    public function getServerVarNames()
    {
        return array_keys($this->getAllServerVars());
    }

    public function hasServerVar($name)
    {
        return array_key_exists($name, $this->getAllServerVars());
    }

    public function hasAnyServerVars()
    {
        return (count($this->getAllServerVars()) > 0);
    }

    protected function removeServerVar($name)
    {
        if ($this->hasServerVar($name)) {
            unset($this->server_vars[$name]);
        }

        return $this;
    }

    protected function removeManyServerVars(array $names)
    {
        foreach ($names as $name) {
            $this->removeServerVar($name);
        }

        return $this;
    }

    protected function removeAllServerVars()
    {
        return $this->removeManyServerVars($this->getServerVarNames());
    }

    protected function replaceServerVars(array $server_vars)
    {
        return $this->removeAllServerVars()->setManyServerVars($server_vars);
    }

    protected function setServerVar($name, $value)
    {
        $this->server_vars[$name] = $value;

        return $this;
    }

    protected function setManyServerVars(array $server_vars)
    {
        foreach ($server_vars as $name => $value) {
            $this->setServerVar($name, $value);
        }

        return $this;
    }

    public function addListener($name, callable $listener, $priority = 0)
    {
        $this->events[$name][$priority][] = $listener;

        ksort($this->events[$name], SORT_NUMERIC);

        return $this;
    }

    public function addManyListeners($name, array $listeners)
    {
        foreach ($listeners as $config) {

            static::normalizeListenerConfig($config, $listener, $priority);

            $this->addListener($name, $listener, $priority);

        }

        return $this;
    }

    public function hasListener($name, callable $listener)
    {
        foreach ($this->getAllListeners($name) as $priority => $listeners) {
            if (in_array($listeners, $listener)) {
                return true;
            }
        }

        return false;
    }

    public function hasAnyListeners($name)
    {
        return (count($this->getAllListeners($name)) > 0);
    }

    public function getAllListeners($name)
    {
        return array_key_exists($name, $this->events) ? $this->events[$name] : [];
    }

    public function removeListener($name, callable $listener)
    {
        foreach ($this->getAllListeners($name) as $priority => $listeners) {

            foreach ($listeners as $listener) {

                $index = array_search($this->events[$name][$priority], $listener);

                if ($index !== false) {
                    unset($this->events[$name][$priority][$index]);
                }

            }

        }

        return $this;
    }

    public function removeManyListeners($name, array $listeners)
    {
        foreach ($listeners as $config) {

            static::normalizeListenerConfig($config, $listener, $priority);

            $this->removeListener($name, $listener);

        }

        return $this;
    }

    public function removeAllListeners($name)
    {
        foreach ($this->getAllListeners($name) as $priority => $listeners) {
            $this->removeListener($name, $listener);
        }

        return $this;
    }

    public function replaceListeners($name, array $listeners)
    {
        $this->removeAllListeners($name)->addManyListeners($name, $listeners);
    }

    public function addSubscriber(\Panadas\EventSubscriberAbstract $subscriber)
    {
        foreach ($subscriber->__subscribe() as $name => $listeners) {

            if (is_callable($listeners)) {
                $this->addListener($name, $listeners);
            } else {
                $this->addManyListeners($name, $listeners);
            }

        }

        return $this;
    }

    public function removeSubscriber(\Panadas\EventSubscriberAbstract $subscriber)
    {
        foreach ($subscriber->__subscribe() as $name => $listeners) {

            if (is_callable($listeners)) {
                $this->removeListener($name, $listeners);
            } else {
                $this->removeManyListeners($name, $listeners);
            }

        }

        return $this;
    }

    public function publish($name, array $params = [])
    {
        $event = new \Panadas\Event($this, $name, $params);

        foreach ($this->getAllListeners($name) as $priority => $listeners) {

            foreach ($listeners as $listener) {

                $listener($event);

                if ($event->isStopped()) {
                    break 2;
                }

            }

        }

        return $event;
    }

    public function errorHandler($errno, $errstr, $errfile, $errline)
    {
        if ($errno &~ error_reporting()) {
            return;
        }

        // E_STRICT errors will not call autoloaders
        // https://bugs.php.net/bug.php?id=54054
        if (!class_exists("Panadas\ErrorException")) {
            require_once __DIR__ . DIRECTORY_SEPARATOR . "ErrorException.php";
        }

        throw new \Panadas\ErrorException($errstr, $errno, 0, $errfile, $errline);
    }

    public function exceptionHandler(\Exception $exception)
    {
        echo "FATAL: {$exception}";

        $exit_code = $exception->getCode();
        if ($exit_code < 1) {
            $exit_code = 1;
        }

        exit($exit_code);
    }

    public function isDebugMode()
    {
        return $this->hasEnvVar("debug");
    }

    public function isRunning()
    {
        return $this->hasMasterRequest();
    }

    public function run(\Panadas\Request $request)
    {
        try {

            if ($this->isRunning()) {
                throw new \RuntimeException("Application is already running");
            }

            $this
                ->setMasterRequest($request)
                ->setActiveRequest($request);

            try {

                $params = [
                    "request" => $request,
                    "response" => null,
                    "action_name" => null,
                    "action_args" => []
                ];

                $event = $this->publish("run", $params);

                $response = $event->get("response");

                if (is_null($response)) {

                    $action_name = $event->get("action_name");
                    $action_args = $event->get("action_args");

                    if (is_null($action_name)) {

                        if ($this->isDebugMode()) {
                            throw new \RuntimeException("An action name was not provided");
                        }

                        $response = $this->error404();

                    }

                    $response = $this->forward($action_name, $action_args);

                }

            } catch (\Exception $exception) {

                $response = $this->exception($exception);

            }

            $send_response = function(\Panadas\ResponseAbstract $response) {

                $params = [
                    "request" => $this->getActiveRequest(),
                    "response" => $response,
                ];

                $event = $this->publish("send", $params);

                $request = $event->get("request");
                $response = $event->get("response");

                return $response->send($request);

            };

            try {

                $send_response($response);

            } catch (\Exception $exception) {

                $send_response($this->exception($exception));

            }

        } catch (\Exception $exception) {

            $this->exceptionHandler($exception);

        }

        return $response;
    }

    public function forward($action_name, array $action_args = [])
    {
        if (!$this->isRunning()) {
            throw new \RuntimeException("Application is not running");
        }

        $request = clone $this->getMasterRequest();
        $this->setActiveRequest($request);

        $params = [
            "request" => $request,
            "response" => null,
            "action_name" => $action_name,
            "action_args" => $action_args
        ];

        $event = $this->publish("forward", $params);

        $request = $event->get("request");
        $response = $event->get("response");

        if (is_null($response)) {

            $action_name = $event->get("action_name");
            $action_args = (array) $event->get("action_args");
            $action_class= \Panadas\Controller\ActionAbstract::getClassName($action_name);

            $action = new $action_class($this, $action_name, $action_args);

            $vars = [
                "action" => $action,
                "request" => $request
            ];

            $response = $action->run($request, $this->factory("response", $vars));

        }

        return $response;
    }

    public function httpError($status_code, $message = null, array $action_args = [])
    {
        $action_args["status_code"] = $status_code;
        $action_args["message"] = $message;

        return $this->forward(static::ACTION_HTTP_ERROR, $action_args);
    }

    public function error400($message = null, array $action_args = [])
    {
        return $this->httpError(400, $message, $action_args);
    }

    public function error401($message = null, array $action_args = [])
    {
        return $this->httpError(401, $message, $action_args);
    }

    public function error403($message = null, array $action_args = [])
    {
        return $this->httpError(403, $message, $action_args);
    }

    public function error404($message = null, array $action_args = [])
    {
        return $this->httpError(404, $message, $action_args);
    }

    public function error500($message = null, array $action_args = [])
    {
        return $this->httpError(500, $message, $action_args);
    }

    public function exception(\Exception $exception, array $action_args = [])
    {
        $action_args = [
            "exception" => $exception
        ];

        return $this->forward(static::ACTION_EXCEPTION, $action_args);
    }

    public function redirect($uri, $status_code = 302, array $action_args = [])
    {
        $action_args["uri"] = $uri;
        $action_args["status_code"] = $status_code;

        return $this->forward(static::ACTION_REDIRECT, $action_args);
    }

    public function factory($filename, array $vars = [])
    {
        $vars["app"] = $this;

        return $this->getLoader()->factory($filename, $vars);
    }

    protected static function normalizeListenerConfig($config, &$listener, &$priority)
    {
        $priority = 0;

        if (is_array($config) && (array_key_exists("priority", $config) || array_key_exists("listener", $config))) {

            if (!array_key_exists("listener", $config)) {
                throw new \Exception("A listener to must be provided for event: {$name}");
            }

            $listener = $config["listener"];

            if (array_key_exists("priority", $config)) {
                $priority = $config["priority"];
            }

        } else {

            $listener = $config;

        }

        return true;
    }

}
