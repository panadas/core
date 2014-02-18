<?php
namespace Panadas\Framework;

use Panadas\EventModule\DataStructure\EventParams;
use Panadas\EventModule\Event;
use Panadas\EventModule\Publisher;
use Panadas\Framework\DataStructure\ActionArgs;
use Panadas\Framework\DataStructure\Services;
use Panadas\HttpMessageModule\Request;
use Panadas\HttpMessageModule\Response;

class Application extends Publisher
{

    private $rootDir;
    private $name;
    private $services;
    private $environment;
    private $debugMode = false;
    private $originalRequest;

    const ENVIRONMENT_PROD = "prod";
    const ENVIRONMENT_TEST = "test";
    const ENVIRONMENT_DEV  = "dev";

    public function __construct(
        $name,
        Services $services = null,
        $environment = self::ENVIRONMENT_PROD,
        $debugMode = false,
        $rootDir = null
    ) {
        parent::__construct();

        if (null === $rootDir) {
            $rootDir = __DIR__ . "/../../../../";
        }

        if (null === $services) {
            $services = new Services();
        }

        $this
            ->setRootDir($rootDir)
            ->setName($name)
            ->setServices($services)
            ->setEnvironment($environment)
            ->setDebugMode($debugMode);

        (new ExceptionHandler($this))->register();
        (new ErrorHandler($this))->register();
    }

    public function getRootDir()
    {
        return $this->rootDir;
    }

    protected function setRootDir($rootDir)
    {
        $realpath = realpath($rootDir);
        if (false === $realpath) {
            throw new \InvalidArgumentException("Invalid root directory: {$rootDir}");
        }

        $this->rootDir = $realpath;

        return $this;
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

    public function getServices()
    {
        return $this->services;
    }

    protected function setServices(Services $services)
    {
        $this->services = $services;
        return $this;
    }

    public function getEnvironment()
    {
        return $this->environment;
    }

    protected function setEnvironment($environment)
    {
        $this->environment = $environment;

        return $this;
    }

    public function isDebugMode()
    {
        return $this->debugMode;
    }

    protected function setDebugMode($debugMode)
    {
        $this->debugMode = (bool) $debugMode;

        return $this;
    }

    public function getOriginalRequest()
    {
        return $this->originalRequest;
    }

    public function hasOriginalRequest()
    {
        return (null !== $this->getOriginalRequest());
    }

    protected function setOriginalRequest(Request $originalRequest = null)
    {
        $this->originalRequest = $originalRequest;

        return $this;
    }

    protected function removeOriginalRequest()
    {
        return $this->setOriginalRequest(null);
    }

    public function publish($name, callable $callback, EventParams $params = null)
    {
        $logger = $this->getServices()->get("logger");
        if (null !== $logger) {
            $logger->debug("Publishing \"{$name}\" event");
        }

        return parent::publish($name, $callback, $params);
    }

    public function getAbsolutePath($relativePath, $rootDir = null)
    {
        if (null === $rootDir) {
            $rootDir = $this->getRootDir();
        }

        return $rootDir . DIRECTORY_SEPARATOR . trim($relativePath, DIRECTORY_SEPARATOR);
    }

    public function getRelativePath($absolutePath, $rootDir = null)
    {
        if (null === $rootDir) {
            $rootDir = $this->getRootDir();
        }

        $rootDir = rtrim($rootDir, DIRECTORY_SEPARATOR);
        $rootDirLength = mb_strlen($rootDir);

        if (mb_substr($absolutePath, 0, $rootDirLength) !== $rootDir) {
            throw new \InvalidArgumentException("Absolute path is not within root directory");
        }

        return "." . mb_substr($absolutePath, $rootDirLength);
    }

    public function isHandling()
    {
        return $this->hasOriginalRequest();
    }

    public function handle(Request $request, $actionClass = null, ActionArgs $actionArgs = null)
    {
        if (null === $actionArgs) {
            $actionArgs = new ActionArgs();
        }

        if ($this->isHandling()) {
            throw new \RuntimeException("Application is already handling a request");
        }

        $this->setOriginalRequest($request);

        $event = $this->publish(
            "handle",
            function (Event $event) {

                $eventParams = $event->getParams();

                $response = $eventParams->get("response");

                if (!$response instanceof Response) {

                    $actionClass = $eventParams->get("actionClass");

                    if (null === $actionClass) {
                        $response = $this->httpError404();
                    } else {
                        $actionArgs = $eventParams->get("actionArgs");
                        $response = $this->subrequest($actionClass, $actionArgs);
                    }

                }

                $eventParams->set("response", $response);

            },
            (new EventParams())
                ->set("request", $request)
                ->set("actionClass", $actionClass)
                ->set("actionArgs", $actionArgs)
        );

        $response = $event->getParams()->get("response");
        if (!$response instanceof Response) {
            throw new \RuntimeException("A response was not provided");
        }

        return $response;
    }

    public function subrequest($actionClass, ActionArgs $actionArgs = null)
    {
        if (null === $actionArgs) {
            $actionArgs = new ActionArgs();
        }

        if (!$this->isHandling()) {
            throw new \RuntimeException("Application has not handled the original request");
        }

        if (!class_exists($actionClass)) {
            throw new \RuntimeException("Class {$actionClass} not found");
        }

        $abstractClass = __NAMESPACE__ . "\Action\AbstractAction";
        if (!is_subclass_of($actionClass, $abstractClass)) {
            throw new \RuntimeException("Class {$actionClass} must extend {$abstractClass}");
        }

        $event = $this->publish(
            "subrequest",
            function (Event $event) {

                $eventParams = $event->getParams();

                $response = $eventParams->get("response");

                if (!$response instanceof Response) {
                    $action = $eventParams->get("action");
                    $request = $eventParams->get("request");
                    $response = $action->handle($request);
                }

                $eventParams->set("response", $response);

            },
            (new EventParams())
                ->set("request", clone $this->getOriginalRequest())
                ->set("action", new $actionClass($this, $actionArgs))
        );

        return $event->getParams()->get("response");
    }

    public function redirect($uri, $statusCode = 302)
    {
        return $this->subrequest("Panadas\Framework\Action\Redirect", [
            "uri" => $uri,
            "statusCode" => $statusCode
        ]);
    }

    public function httpError($statusCode, $message = null)
    {
        return $this->subrequest("Panadas\Framework\Action\HttpError", [
            "statusCode" => $statusCode,
            "message" => $message
        ]);
    }

    public function httpError400($message = null)
    {
        return $this->httpError(400, $message);
    }

    public function httpError401($message = null)
    {
        return $this->httpError(401, $message);
    }

    public function httpError403($message = null)
    {
        return $this->httpError(403, $message);
    }

    public function httpError404($message = null)
    {
        return $this->httpError(404, $message);
    }

    public function send(Response $response)
    {
        $this->publish(
            "send",
            function (Event $event) {

                $eventParams = $event->getParams();

                $request = $eventParams->get("request");
                $response = $eventParams->get("response");

                if ($request->isHead() && $response->hasContent()) {

                    $response->getHeaders()->set(
                        "Content-Length",
                        mb_strlen($response->getContent(), $response->getCharset())
                    );

                    $response->removeContent();

                }

                $response->send();

            },
            (new EventParams())
                ->set("request", clone $this->getOriginalRequest())
                ->set("response", $response)
        );

        return $this;
    }
}
