<?php
namespace Panadas\Kernel;

class HttpKernel extends \Panadas\Kernel\AbstractKernel
{

    private $originalRequest;
    private $currentRequest;

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
        parent::__construct($name, $loader, $eventPublisher, $serviceContainerCallback, $serverParams, $envParams);

        (new \Panadas\Error\ExceptionHandler($this))->register();
        (new \Panadas\Error\ErrorHandler($this))->register();
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
     * @return \Panadas\Kernel\HttpKernel
     */
    protected function setOriginalRequest(\Panadas\Http\Request $originalRequest = null)
    {
        $this->originalRequest = $originalRequest;

        return $this;
    }

    /**
     * @return \Panadas\Kernel\HttpKernel
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
     * @return \Panadas\Kernel\HttpKernel
     */
    protected function setCurrentRequest(\Panadas\Http\Request $currentRequest = null)
    {
        $this->currentRequest = $currentRequest;

        return $this;
    }

    /**
     * @return \Panadas\Kernel\HttpKernel
     */
    protected function removeCurrentRequest()
    {
        return $this->setCurrentRequest(null);
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
}
