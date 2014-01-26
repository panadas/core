<?php
namespace Panadas\Http;

class Kernel extends \Panadas\Event\Publisher
{

    private $name;
    private $loader;
    private $service_container;
    private $original_request;
    private $current_request;
    private $server_vars = [];
    private $env_vars = [];

    const ENV_DEBUG = "PANADAS_DEBUG";
    const ACTION_EXCEPTION = "Exception";
    const ACTION_HTTP_ERROR = "HttpError";
    const ACTION_REDIRECT = "Redirect";

    public function __construct($name, \Panadas\Loader $loader, \Panadas\Event\Publisher $event_publisher, callable $service_container_callback, array $server_vars = [], array $env_vars = [])
    {
        parent::__construct();

        $this
            ->setName($name)
            ->replaceServerVars($server_vars)
            ->replaceEnvVars($env_vars)
            ->setLoader($loader)
            ->setServiceContainer($service_container_callback($this));

        if ( ! ini_get("date.timezone")) {
            date_default_timezone_set("UTC");
        }

        (new \Panadas\Error\ExceptionHandler($this))->register();
        (new \Panadas\Error\ErrorHandler($this))->register();
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

    protected function getOriginalRequest()
    {
        return $this->original_request;
    }

    protected function hasOriginalRequest()
    {
        return (null !== $this->getOriginalRequest());
    }

    protected function setOriginalRequest(\Panadas\Http\Request $original_request = null)
    {
        $this->original_request = $original_request;

        return $this;
    }

    protected function removeOriginalRequest()
    {
        return $this->setOriginalRequest(null);
    }

    protected function getCurrentRequest()
    {
        return $this->current_request;
    }

    protected function hasCurrentRequest()
    {
        return (null !== $this->getCurrentRequest());
    }

    protected function setCurrentRequest(\Panadas\Http\Request $current_request = null)
    {
        $this->current_request = $current_request;

        return $this;
    }

    protected function removeCurrentRequest()
    {
        return $this->setCurrentRequest(null);
    }

    public function getServiceContainer()
    {
        return $this->service_container;
    }

    protected function setServiceContainer(\Panadas\Service\Container $service_container)
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

    public function isDebugMode()
    {
        return $this->hasEnvVar(static::ENV_DEBUG);
    }

    public function isHandling()
    {
        return $this->hasOriginalRequest();
    }

    public function handle(\Panadas\Http\Request $request)
    {
        if ($this->isHandling()) {
            throw new \RuntimeException("Application is already running");
        }

        $this->setOriginalRequest($request);

        try {

            $params = [
                "request" => $request,
                "response" => null,
                "action_name" => null,
                "action_args" => []
            ];

            $event = $this->publish("handle", $params);

            $response = $event->get("response");

            if (null === $response) {

                $action_name = $event->get("action_name");
                $action_args = $event->get("action_args");

                if (null !== $action_name) {

                    $response = $this->forward($action_name, $action_args);

                } elseif ( ! $this->isDebugMode()) {

                    $response = $this->error404();

                } else {

                    throw new \RuntimeException("An action name was not provided");

                }

            }

            $response = $this->send($response);

        } catch (\Exception $exception) {

            $response = $this->send($this->exception($exception));

        }

        return $response;
    }

    public function forward($action_name, array $action_args = [])
    {
        if ( ! $this->isHandling()) {
            throw new \RuntimeException("Application is not running");
        }

        $request = clone $this->getOriginalRequest();
        $this->setCurrentRequest($request);

        $params = [
            "request" => $request,
            "response" => null,
            "action_name" => $action_name,
            "action_args" => $action_args
        ];

        $event = $this->publish("forward", $params);

        $request = $event->get("request");
        $response = $event->get("response");

        if (null === $response) {

            $action_name = $event->get("action_name");
            $action_args = $event->get("action_args");
            $action_class= \Panadas\Controller\AbstractAction::getClassName($action_name);

            $action = new $action_class($this, $action_name, $action_args);

            $vars = [
                "action" => $action,
                "request" => $request
            ];

            $response = $action->handle($request);

        }

        return $response;
    }

    public function send(\Panadas\Http\Response $response)
    {
        $params = [
            "request" => $this->getCurrentRequest(),
            "response" => $response,
        ];

        $event = $this->publish("send", $params);

        return $event->get("response")->send();
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

    public static function create($name, \Panadas\Loader $loader = null, \Panadas\Event\Publisher $event_publisher = null, callable $service_container_callback = null)
    {
        if (null === $loader) {
            $loader = new \Panadas\Loader(__DIR__ . "/../../../../../../");
        }

        if (null === $event_publisher) {
            $event_publisher = new \Panadas\Event\Publisher();
        }

        if (null === $service_container_callback) {
            $service_container_callback = function(\Panadas\Http\Kernel $kernel) {
                return new \Panadas\Service\Container($kernel);
            };
        }

        return new static($name, $loader, $event_publisher, $service_container_callback, $_SERVER, $_ENV);
    }

}
