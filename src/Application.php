<?php
namespace Panadas\Framework;

use Panadas\EventManager\Publisher;
use Panadas\DataStructure\Hash;
use Panadas\HttpMessage\Request;
use Panadas\HttpMessage\Response;

class Application extends Publisher
{

    private $name;

    private $services;

    private $debugMode = false;

    const ENVIRONMENT_PROD = "prod";

    const ENVIRONMENT_TEST = "test";

    const ENVIRONMENT_DEV = "dev";

    public function __construct($name, array $services = [], $environment = self::ENVIRONMENT_PROD, $debugMode = false)
    {
        parent::__construct();

        $this
            ->setName($name)
            ->setServices(new ServicesHash($this, $services))
            ->setDebugMode($debugMode);
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

    public function isDebugMode()
    {
        return $this->debugMode;
    }

    protected function setDebugMode($debugMode)
    {
        $this->debugMode = (bool) $debugMode;

        return $this;
    }

    public function handle(Request $request, $actionClass = null, array $actionArgs = [])
    {
        $event = $this->publish("handle", [
            "application" => $this,
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
            $actionArgs = [
                "statusCode" => 404
            ];
        }

        return $this->subrequest($request, $actionClass, $actionArgs);
    }

    public function subrequest(Request $request, $actionClass, array $actionArgs = [])
    {
        if (! class_exists($actionClass)) {
            throw new \RuntimeException("Class {$actionClass} not found");
        }

        $abstractClass = __NAMESPACE__ . "\AbstractAction";

        if (! is_subclass_of($actionClass, $abstractClass)) {
            throw new \RuntimeException("Class {$actionClass} must extend {$abstractClass}");
        }

        $action = new $actionClass($this, $actionArgs);

        $event = $this->publish("subrequest", [
            "application" => $this,
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

        if (! $response instanceof Response) {
            throw new \RuntimeException("A response was not provided by {$actionClass}");
        }

        return $response;
    }
}
