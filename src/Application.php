<?php
namespace Panadas\Framework;

use Panadas\DataStructure\Hash;
use Panadas\EventManager\Publisher;
use Panadas\Framework\DataStructure\ServicesHash;
use Panadas\HttpMessage\Request;
use Panadas\HttpMessage\Response;

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
        array $services = [],
        $environment = self::ENVIRONMENT_PROD,
        $debugMode = false,
        $rootDir = null
    ) {
        parent::__construct();

        if (null === $rootDir) {
            $rootDir = __DIR__ . "/../../../../";
        }

        register_shutdown_function([$this, "shutdown"]);

        $this
            ->setRootDir($rootDir)
            ->setName($name)
            ->setEnvironment($environment)
            ->setDebugMode($debugMode)
            ->setServices(new ServicesHash($this, $services));
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

    protected function setServices(ServicesHash $services)
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

    public function publish($name, array $params = [])
    {
        $logger = $this->getServices()->get("logger");
        if ($logger) {
            $logger->info("Publishing event: {$name}");
        }

        return parent::publish($name, $params);
    }

    public function handle(Request $request, $actionClass = null, array $actionArgs = [])
    {
        if ($this->isHandling()) {
            throw new \RuntimeException("Application is already handling a request");
        }

        $this->setOriginalRequest($request);

        $event = $this->publish("handle", [
            "request" => $request,
            "response" => null,
            "actionClass" => $actionClass,
            "actionArgs" => new Hash($actionArgs)
        ]);

        $eventParams = $event->getParams();

        $response = $eventParams->get("response");
        if ($response instanceof Response) {
            return $response;
        }

        $actionClass = $eventParams->get("actionClass");
        if ($actionClass) {
            $actionArgs = $eventParams->get("actionArgs")->all();
        } else {
            $actionClass = "HttpErrorAction";
            $actionArgs = ["statusCode" => 404];
        }

        return $this->subrequest($actionClass, $actionArgs);
    }

    public function subrequest($actionClass, array $actionArgs = [])
    {
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

        $action = new $actionClass($this, $actionArgs);
        $request = clone $this->getOriginalRequest();

        $event = $this->publish("subrequest", [
            "request" => $request,
            "response" => null,
            "action" => $action
        ]);

        $eventParams = $event->getParams();

        $response = $eventParams->get("response");
        if ($response instanceof Response) {
            return $response;
        }

        $response = $action->handle($request);

        if (!$response instanceof Response) {
            throw new \RuntimeException("A response was not provided by {$actionClass}");
        }

        return $response;
    }

    public function send(Response $response)
    {
        $event = $this->publish("send", [
            "response" => $response
        ]);

        if ($this->getOriginalRequest()->isHead() && $response->hasContent()) {

            $response->getHeaders()->set(
                "Content-Length",
                mb_strlen($response->getContent(), $response->getCharset())
            );

            $response->removeContent();

        }

        $response->send();

        return $this;
    }

    public function shutdown()
    {
        $this->publish("shutdown");
    }
}
