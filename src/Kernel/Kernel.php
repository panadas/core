<?php
namespace Panadas\Kernel;

class Kernel extends \Panadas\AbstractBase
{

    private $name;
    private $loader;
    private $eventPublisher;
    private $serviceContainer;
    private $serverParams;
    private $envParams;

    const ENV_DEBUG = "PANADAS_DEBUG";

    const ACTION_CLASS_DEFAULT = "Panadas\Controller\DefaultActionController";
    const ACTION_CLASS_HTTP_ERROR = "Panadas\Controller\HttpErrorActionController";
    const ACTION_CLASS_REDIRECT = "Panadas\Controller\HttpRedirectActionController";

    /**
     * @param string          $name
     * @param \Panadas\Loader $loader
     * @param callable        $eventPublisherCallback
     * @param callable        $serviceContainerCallback
     * @param array           $serverParams
     * @param array           $envParams
     */
    public function __construct(
        $name,
        \Panadas\Loader $loader,
        callable $eventPublisherCallback,
        callable $serviceContainerCallback,
        array $serverParams = [],
        array $envParams = []
    ) {
        parent::__construct();

        $this
            ->setName($name)
            ->setLoader($loader)
            ->setEventPublisher($eventPublisherCallback($this))
            ->setServerParams(new \Panadas\DataStructure\HashDataStructure($serverParams))
            ->setEnvParams(new \Panadas\DataStructure\HashDataStructure($envParams))
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
     * @return \Panadas\Kernel\Kernel
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
     * @return \Panadas\Kernel\Kernel
     */
    protected function setLoader(\Panadas\Loader $loader)
    {
        $this->loader = $loader;

        return $this;
    }

    /**
     * @return \Panadas\Event\EventPublisher
     */
    public function getEventPublisher()
    {
        return $this->eventPublisher;
    }

    /**
     * @param  \Panadas\Event\EventPublisher $eventPublisher
     * @return \Panadas\Kernel\Kernel
     */
    protected function setEventPublisher(\Panadas\Event\EventPublisher $eventPublisher)
    {
        $this->eventPublisher = $eventPublisher;

        return $this;
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
     * @return \Panadas\Kernel\Kernel
     */
    protected function setServiceContainer(\Panadas\Service\ServiceContainer $serviceContainer)
    {
        $this->serviceContainer = $serviceContainer;

        return $this;
    }

    /**
     * @return \Panadas\DataStructure\HashDataStructure
     */
    protected function getServerParams()
    {
        return $this->serverParams;
    }

    /**
     * @param  \Panadas\DataStructure\HashDataStructure $serverParams
     * @return \Panadas\Kernel\Kernel
     */
    protected function setServerParams(\Panadas\DataStructure\HashDataStructure $serverParams)
    {
        $this->serverParams = $serverParams;

        return $this;
    }

    /**
     * @return \Panadas\DataStructure\HashDataStructure
     */
    protected function getEnvParams()
    {
        return $this->envParams;
    }

    /**
     * @param  \Panadas\DataStructure\HashDataStructure $envParams
     * @return \Panadas\Kernel\Kernel
     */
    protected function setEnvParams(\Panadas\DataStructure\HashDataStructure $envParams)
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
     * @return \Panadas\Kernel\Kernel
     */
    public function removeServerParam($name)
    {
        $this->getServerParams()->remove($name);

        return $this;
    }

    /**
     * @return \Panadas\Kernel\Kernel
     */
    public function removeAllServerParams()
    {
        $this->getServerParams()->removeAll();

        return $this;
    }

    /**
     * @param  string $name
     * @param  mixed  $value
     * @return \Panadas\Kernel\Kernel
     */
    public function setServerParam($name, $value)
    {
        $this->getServerParams()->set($name, $value);

        return $this;
    }

    /**
     * @param  array $serverParams
     * @return \Panadas\Kernel\Kernel
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
     * @return \Panadas\Kernel\Kernel
     */
    public function removeEnvParam($name)
    {
        $this->getEnvParams()->remove($name);

        return $this;
    }

    /**
     * @return \Panadas\Kernel\Kernel
     */
    public function removeAllEnvParams()
    {
        $this->getEnvParams()->removeAll();

        return $this;
    }

    /**
     * @param  string $name
     * @param  mixed  $value
     * @return \Panadas\Kernel\Kernel
     */
    public function setEnvParam($name, $value)
    {
        $this->getEnvParams()->set($name, $value);

        return $this;
    }

    /**
     * @param  array $envParams
     * @return \Panadas\Kernel\Kernel
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
     * @param  \Panadas\Http\Request $request
     * @throws \RuntimeException
     * @return \Panadas\Http\Response
     */
    public function handle(\Panadas\Http\Request $request)
    {
        $params = [
            "request" => $request,
            "response" => null,
            "actionClass" => null,
            "actionArgs" => []
        ];

        $event = $this->getEventPublisher()->publish("handle", $params);

        $request = $event->get("request");
        $response = $event->get("response");

        if (null === $response) {

            $actionClass = $event->get("actionClass");
            $actionArgs = $event->get("actionArgs");

            if (null !== $actionClass) {

                $response = $request->forward($actionClass, $actionArgs);

            } else {

                if (!$this->isDebugMode()) {
                    $response = $request->errorNotFound();
                } else {
                    throw new \RuntimeException("An action name was not provided");
                }

            }

        }

        return $this->send($request, $response);
    }

    /**
     * @param  \Panadas\Http\Request  $request
     * @param  \Panadas\Http\Response $response
     * @return \Panadas\Http\Response
     */
    protected function send(\Panadas\Http\Request $request, \Panadas\Http\Response $response)
    {
        if ($request->isHead()) {
            $response
                ->setHeader("Content-Length", mb_strlen($response->getContent(), $response->getCharset()))
                ->removeContent();
        }

        $params = [
            "request" => $request,
            "response" => $response
        ];

        $event = $this->getEventPublisher()->publish("send", $params);

        return $event->get("response")->send();
    }

    /**
     * @param  string $name
     * @return \Panadas\Kernel\Kernel
     */
    public static function create($name)
    {
        return new static(
            $name,
            \Panadas\Loader::create(),
            function (\Panadas\Kernel\Kernel $kernel) {
                return \Panadas\Event\EventPublisher::create($kernel);
            },
            function (\Panadas\Kernel\Kernel $kernel) {
                return \Panadas\Service\ServiceContainer::create($kernel);
            },
            $_SERVER,
            $_ENV
        );
    }
}
