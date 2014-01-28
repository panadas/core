<?php
namespace Panadas\Http;

class Kernel extends \Panadas\Event\Publisher
{

    private $name;
    private $loader;
    private $serviceContainer;
    private $originalRequest;
    private $currentRequest;
    private $serverVars = [];
    private $envVars = [];

    const ENV_DEBUG = "PANADAS_DEBUG";
    const ACTION_EXCEPTION = "Exception";
    const ACTION_HTTP_ERROR = "HttpError";
    const ACTION_REDIRECT = "Redirect";

    public function __construct(
        $name,
        \Panadas\Loader $loader,
        \Panadas\Event\Publisher $eventPublisher,
        callable $serviceContainerCallback,
        array $serverVars = [],
        array $envVars = []
    ) {
        parent::__construct();

        $this
            ->setName($name)
            ->replaceServerVars($serverVars)
            ->replaceEnvVars($envVars)
            ->setLoader($loader)
            ->setServiceContainer($serviceContainerCallback($this));

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
        return $this->originalRequest;
    }

    protected function hasOriginalRequest()
    {
        return (null !== $this->getOriginalRequest());
    }

    protected function setOriginalRequest(\Panadas\Http\Request $originalRequest = null)
    {
        $this->originalRequest = $originalRequest;

        return $this;
    }

    protected function removeOriginalRequest()
    {
        return $this->setOriginalRequest(null);
    }

    protected function getCurrentRequest()
    {
        return $this->currentRequest;
    }

    protected function hasCurrentRequest()
    {
        return (null !== $this->getCurrentRequest());
    }

    protected function setCurrentRequest(\Panadas\Http\Request $currentRequest = null)
    {
        $this->currentRequest = $currentRequest;

        return $this;
    }

    protected function removeCurrentRequest()
    {
        return $this->setCurrentRequest(null);
    }

    public function getServiceContainer()
    {
        return $this->serviceContainer;
    }

    protected function setServiceContainer(\Panadas\Service\Container $serviceContainer)
    {
        $this->serviceContainer = $serviceContainer;

        return $this;
    }

    public function getEnvVar($name, $default = null)
    {
        return $this->hasEnvVar($name) ? $this->envVars[$name] : $default;
    }

    public function getAllEnvVars()
    {
        return $this->envVars;
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
            unset($this->envVars[$name]);
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

    public function replaceEnvVars(array $envVars)
    {
        return $this
            ->removeAllEnvVars()
            ->setManyEnvVars($envVars);
    }

    public function setEnvVar($name, $value)
    {
        $this->envVars[$name] = $value;
        return $this;
    }

    public function setManyEnvVars(array $envVars)
    {
        foreach ($envVars as $name => $value) {
            $this->setEnvVar($name, $value);
        }

        return $this;
    }

    public function getServerVar($name, $default = null)
    {
        return $this->hasServerVar($name) ? $this->serverVars[$name] : $default;
    }

    public function getAllServerVars()
    {
        return $this->serverVars;
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
            unset($this->serverVars[$name]);
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

    protected function replaceServerVars(array $serverVars)
    {
        return $this->removeAllServerVars()->setManyServerVars($serverVars);
    }

    protected function setServerVar($name, $value)
    {
        $this->serverVars[$name] = $value;

        return $this;
    }

    protected function setManyServerVars(array $serverVars)
    {
        foreach ($serverVars as $name => $value) {
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
                "actionName" => null,
                "actionArgs" => []
            ];

            $event = $this->publish("handle", $params);

            $response = $event->get("response");

            if (null === $response) {

                $actionName = $event->get("actionName");
                $actionArgs = $event->get("actionArgs");

                if (null !== $actionName) {

                    $response = $this->forward($actionName, $actionArgs);

                } elseif (!$this->isDebugMode()) {

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

    public function forward($actionName, array $actionArgs = [])
    {
        if (!$this->isHandling()) {
            throw new \RuntimeException("Application is not running");
        }

        $request = clone $this->getOriginalRequest();
        $this->setCurrentRequest($request);

        $params = [
            "request" => $request,
            "response" => null,
            "actionName" => $actionName,
            "actionArgs" => $actionArgs
        ];

        $event = $this->publish("forward", $params);

        $request = $event->get("request");
        $response = $event->get("response");

        if (null === $response) {

            $actionName = $event->get("actionName");
            $actionArgs = $event->get("actionArgs");
            $actionClass= \Panadas\Controller\AbstractActionController::getClassName($actionName);

            $action = new $actionClass($this, $actionName, $actionArgs);

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
            "response" => $response
        ];

        $event = $this->publish("send", $params);

        return $event->get("response")
            ->send();
    }

    public function httpError($statusCode, $message = null, array $actionArgs = [])
    {
        $actionArgs["statusCode"] = $statusCode;
        $actionArgs["message"] = $message;

        return $this->forward(static::ACTION_HTTP_ERROR, $actionArgs);
    }

    public function error400($message = null, array $actionArgs = [])
    {
        return $this->httpError(400, $message, $actionArgs);
    }

    public function error401($message = null, array $actionArgs = [])
    {
        return $this->httpError(401, $message, $actionArgs);
    }

    public function error403($message = null, array $actionArgs = [])
    {
        return $this->httpError(403, $message, $actionArgs);
    }

    public function error404($message = null, array $actionArgs = [])
    {
        return $this->httpError(404, $message, $actionArgs);
    }

    public function error500($message = null, array $actionArgs = [])
    {
        return $this->httpError(500, $message, $actionArgs);
    }

    public function exception(\Exception $exception, array $actionArgs = [])
    {
        $actionArgs = [
            "exception" => $exception
        ];

        return $this->forward(static::ACTION_EXCEPTION, $actionArgs);
    }

    public function redirect($uri, $statusCode = 302, array $actionArgs = [])
    {
        $actionArgs["uri"] = $uri;
        $actionArgs["statusCode"] = $statusCode;

        return $this->forward(static::ACTION_REDIRECT, $actionArgs);
    }

    public static function create(
        $name,
        \Panadas\Loader $loader = null,
        \Panadas\Event\Publisher $eventPublisher = null,
        callable $serviceContainerCallback = null
    ) {
        if (null === $loader) {
            $loader = new \Panadas\Loader(__DIR__ . "/../../../../../../");
        }

        if (null === $eventPublisher) {
            $eventPublisher = new \Panadas\Event\Publisher();
        }

        if (null === $serviceContainerCallback) {
            $serviceContainerCallback = function (\Panadas\Http\Kernel $kernel) {
                return new \Panadas\Service\Container($kernel);
            };
        }

        return new static($name, $loader, $eventPublisher, $serviceContainerCallback, $_SERVER, $_ENV);
    }
}
