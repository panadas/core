<?php
namespace Panadas\Http;

class Kernel extends \Panadas\Event\EventPublisher
{

    private $name;
    private $loader;
    private $serviceContainer;
    private $originalRequest;
    private $currentRequest;
    private $serverParams;
    private $envParams;

    const ENV_DEBUG = "PANADAS_DEBUG";
    const ACTION_EXCEPTION = "Exception";
    const ACTION_HTTP_ERROR = "HttpError";
    const ACTION_REDIRECT = "Redirect";

    /**
     * @param string                        $name
     * @param \Panadas\Loader               $loader
     * @param \Panadas\Event\EventPublisher $eventPublisher
     * @param callable                      $serviceContainerCallback
     * @param array                         $serverParams
     * @param array                         $envParams
     */
    public function __construct(
        $name,
        \Panadas\Loader $loader,
        \Panadas\Event\EventPublisher $eventPublisher,
        callable $serviceContainerCallback,
        array $serverParams = [],
        array $envParams = []
    ) {
        parent::__construct();

        $this
            ->setLoader($loader)
            ->setName($name)
            ->setServerParams(new \Panadas\ArrayStore\HashArrayStore($serverParams))
            ->setEnvParams(new \Panadas\ArrayStore\HashArrayStore($envParams))
            ->setServiceContainer($serviceContainerCallback($this));

        (new \Panadas\Error\ExceptionHandler($this))->register();
        (new \Panadas\Error\ErrorHandler($this))->register();

    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param  string $name
     * @return \Panadas\Http\Kernel
     */
    protected function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return \Panadas\Loader
     */
    public function getLoader()
    {
        return $this->loader;
    }

    /**
     * @param  \Panadas\Loader $loader
     * @return \Panadas\Http\Kernel
     */
    protected function setLoader(\Panadas\Loader $loader)
    {
        $this->loader = $loader;

        return $this;
    }

    /**
     * @return \Panadas\Http\Request
     */
    protected function getOriginalRequest()
    {
        return $this->originalRequest;
    }

    /**
     * @return boolean
     */
    protected function hasOriginalRequest()
    {
        return (null !== $this->getOriginalRequest());
    }

    /**
     * @param  \Panadas\Http\Request $originalRequest
     * @return \Panadas\Http\Kernel
     */
    protected function setOriginalRequest(\Panadas\Http\Request $originalRequest = null)
    {
        $this->originalRequest = $originalRequest;

        return $this;
    }

    /**
     * @return \Panadas\Http\Kernel
     */
    protected function removeOriginalRequest()
    {
        return $this->setOriginalRequest(null);
    }

    /**
     * @return \Panadas\Http\Request
     */
    protected function getCurrentRequest()
    {
        return $this->currentRequest;
    }

    /**
     * @return boolean
     */
    protected function hasCurrentRequest()
    {
        return (null !== $this->getCurrentRequest());
    }

    /**
     * @param  \Panadas\Http\Request $currentRequest
     * @return \Panadas\Http\Kernel
     */
    protected function setCurrentRequest(\Panadas\Http\Request $currentRequest = null)
    {
        $this->currentRequest = $currentRequest;

        return $this;
    }

    /**
     * @return \Panadas\Http\Kernel
     */
    protected function removeCurrentRequest()
    {
        return $this->setCurrentRequest(null);
    }

    /**
     * @return \Panadas\Service\ServiceContainer
     */
    public function getServiceContainer()
    {
        return $this->serviceContainer;
    }

    /**
     * @param  \Panadas\Service\ServiceContainer $serviceContainer
     * @return \Panadas\Http\Kernel
     */
    protected function setServiceContainer(\Panadas\Service\ServiceContainer $serviceContainer)
    {
        $this->serviceContainer = $serviceContainer;

        return $this;
    }

    /**
     * @return \Panadas\ArrayStore\HashArrayStore
     */
    protected function getServerParams()
    {
        return $this->serverParams;
    }

    /**
     * @param  \Panadas\ArrayStore\HashArrayStore $serverParams
     * @return \Panadas\Http\Kernel
     */
    protected function setServerParams(\Panadas\ArrayStore\HashArrayStore $serverParams)
    {
        $this->serverParams = $serverParams;

        return $this;
    }

    /**
     * @return \Panadas\ArrayStore\HashArrayStore
     */
    protected function getEnvParams()
    {
        return $this->envParams;
    }

    /**
     * @param  \Panadas\ArrayStore\HashArrayStore $envParams
     * @return \Panadas\Http\Kernel
     */
    protected function setEnvParams(\Panadas\ArrayStore\HashArrayStore $envParams)
    {
        $this->envParams = $envParams;

        return $this;
    }

    /**
     * @param  string $name
     * @param  mixed  $default
     * @return mixed
     */
    public function getServerParam($name, $default = null)
    {
        return $this->getServerParams()->get($name, $default);
    }

    /**
     * @return array
     */
    public function getAllServerParams()
    {
        return $this->getServerParams()->getAll();
    }

    /**
     * @return array
     */
    public function getServerParamNames()
    {
        return $this->getServerParams()->getNames();
    }

    /**
     * @param  string $name
     * @return boolean
     */
    public function hasServerParam($name)
    {
        return $this->getServerParams()->has($name);
    }

    /**
     * @return boolean
     */
    public function hasAnyServerParams()
    {
        return $this->getServerParams()->hasAny();
    }

    /**
     * @param  string $name
     * @return \Panadas\Http\Kernel
     */
    public function removeServerParam($name)
    {
        $this->getServerParams()->remove($name);

        return $this;
    }

    /**
     * @return \Panadas\Http\Kernel
     */
    public function removeAllServerParams()
    {
        $this->getServerParams()->removeAll();

        return $this;
    }

    /**
     * @param  string $name
     * @param  mixed  $value
     * @return \Panadas\Http\Kernel
     */
    public function setServerParam($name, $value)
    {
        $this->getServerParams()->set($name, $value);

        return $this;
    }

    /**
     * @param  array $serverParams
     * @return \Panadas\Http\Kernel
     */
    public function replaceServerParams(array $serverParams)
    {
        $this->getServerParams()->replace($serverParams);

        return $this;
    }

    /**
     * @param  string $name
     * @param  mixed  $default
     * @return mixed
     */
    public function getEnvParam($name, $default = null)
    {
        return $this->getEnvParams()->get($name, $default);
    }

    /**
     * @return array
     */
    public function getAllEnvParams()
    {
        return $this->getEnvParams()->getAll();
    }

    /**
     * @return array
     */
    public function getEnvParamNames()
    {
        return $this->getEnvParams()->getNames();
    }

    /**
     * @param  string $name
     * @return boolean
     */
    public function hasEnvParam($name)
    {
        return $this->getEnvParams()->has($name);
    }

    /**
     * @return boolean
     */
    public function hasAnyEnvParams()
    {
        return $this->getEnvParams()->hasAny();
    }

    /**
     * @param  string $name
     * @return \Panadas\Http\Kernel
     */
    public function removeEnvParam($name)
    {
        $this->getEnvParams()->remove($name);

        return $this;
    }

    /**
     * @return \Panadas\Http\Kernel
     */
    public function removeAllEnvParams()
    {
        $this->getEnvParams()->removeAll();

        return $this;
    }

    /**
     * @param  string $name
     * @param  mixed  $value
     * @return \Panadas\Http\Kernel
     */
    public function setEnvParam($name, $value)
    {
        $this->getEnvParams()->set($name, $value);

        return $this;
    }

    /**
     * @param  array $envParams
     * @return \Panadas\Http\Kernel
     */
    public function replaceEnvParams(array $envParams)
    {
        $this->getEnvParams()->replace($envParams);

        return $this;
    }

    /**
     * @return boolean
     */
    public function isDebugMode()
    {
        return $this->hasEnvParam(static::ENV_DEBUG);
    }

    /**
     * @return boolean
     */
    public function isHandling()
    {
        return $this->hasOriginalRequest();
    }

    /**
     * @param  \Panadas\Http\Request $request
     * @throws \RuntimeException
     * @return \Panadas\Http\Response
     */
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

    /**
     * @param  string $actionName
     * @param  array $actionArgs
     * @throws \RuntimeException
     * @return \Panadas\Http\Response
     */
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

    /**
     * @param \Panadas\Http\Response $response
     */
    public function send(\Panadas\Http\Response $response)
    {
        $params = [
            "request" => $this->getCurrentRequest(),
            "response" => $response
        ];

        $event = $this->publish("send", $params);
        $event->get("response")->send();

        return $this;
    }

    /**
     * @param  integer $statusCode
     * @param  string  $message
     * @param  array   $actionArgs
     * @return \Panadas\Http\Response
     */
    public function httpError($statusCode, $message = null, array $actionArgs = [])
    {
        $actionArgs["statusCode"] = $statusCode;
        $actionArgs["message"] = $message;

        return $this->forward(static::ACTION_HTTP_ERROR, $actionArgs);
    }

    /**
     * @param  string $message
     * @param  array $actionArgs
     * @return \Panadas\Http\Response
     */
    public function error400($message = null, array $actionArgs = [])
    {
        return $this->httpError(400, $message, $actionArgs);
    }

    /**
     * @param  string $message
     * @param  array $actionArgs
     * @return \Panadas\Http\Response
     */
    public function error401($message = null, array $actionArgs = [])
    {
        return $this->httpError(401, $message, $actionArgs);
    }

    /**
     * @param  string $message
     * @param  array $actionArgs
     * @return \Panadas\Http\Response
     */
    public function error403($message = null, array $actionArgs = [])
    {
        return $this->httpError(403, $message, $actionArgs);
    }

    /**
     * @param  string $message
     * @param  array $actionArgs
     * @return \Panadas\Http\Response
     */
    public function error404($message = null, array $actionArgs = [])
    {
        return $this->httpError(404, $message, $actionArgs);
    }

    /**
     * @param  string $message
     * @param  array $actionArgs
     * @return \Panadas\Http\Response
     */
    public function error500($message = null, array $actionArgs = [])
    {
        return $this->httpError(500, $message, $actionArgs);
    }

    /**
     * @param  \Exception $exception
     * @param  array      $actionArgs
     * @return \Panadas\Http\Response
     */
    public function exception(\Exception $exception, array $actionArgs = [])
    {
        $actionArgs = [
            "exception" => $exception
        ];

        return $this->forward(static::ACTION_EXCEPTION, $actionArgs);
    }

    /**
     * @param  string  $uri
     * @param  integer $statusCode
     * @param  array   $actionArgs
     * @return \Panadas\Http\Response
     */
    public function redirect($uri, $statusCode = 302, array $actionArgs = [])
    {
        $actionArgs["uri"] = $uri;
        $actionArgs["statusCode"] = $statusCode;

        return $this->forward(static::ACTION_REDIRECT, $actionArgs);
    }

    /**
     * @param  string $name
     * @return \Panadas\Http\Kernel
     */
    public static function create($name)
    {
        $loader = new \Panadas\Loader(__DIR__ . "/../../../../../../");

        $eventPublisher = new \Panadas\Event\EventPublisher();

        $serviceContainerCallback = function (\Panadas\Http\Kernel $kernel) {
            return new \Panadas\Service\ServiceContainer($kernel);
        };

        return new static($name, $loader, $eventPublisher, $serviceContainerCallback, $_SERVER, $_ENV);
    }
}
