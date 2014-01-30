<?php
namespace Panadas\Kernel;

abstract class AbstractKernel extends \Panadas\Event\EventPublisher
{

    private $name;
    private $loader;
    private $serviceContainer;
    private $serverParams;
    private $envParams;

    const ENV_DEBUG = "PANADAS_DEBUG";

    abstract public function forward($actionName, array $actionArgs = []);

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
            ->setServerParams(new \Panadas\DataStructure\HashDataStructure($serverParams))
            ->setEnvParams(new \Panadas\DataStructure\HashDataStructure($envParams))
            ->setServiceContainer($serviceContainerCallback($this));

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
     * @return \Panadas\Kernel\HttpKernel
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
     * @return \Panadas\Kernel\HttpKernel
     */
    protected function setLoader(\Panadas\Loader $loader)
    {
        $this->loader = $loader;

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
     * @return \Panadas\Kernel\HttpKernel
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
     * @return \Panadas\Kernel\HttpKernel
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
     * @return \Panadas\Kernel\HttpKernel
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
     * @return \Panadas\Kernel\HttpKernel
     */
    public function removeServerParam($name)
    {
        $this->getServerParams()->remove($name);

        return $this;
    }

    /**
     * @return \Panadas\Kernel\HttpKernel
     */
    public function removeAllServerParams()
    {
        $this->getServerParams()->removeAll();

        return $this;
    }

    /**
     * @param  string $name
     * @param  mixed  $value
     * @return \Panadas\Kernel\HttpKernel
     */
    public function setServerParam($name, $value)
    {
        $this->getServerParams()->set($name, $value);

        return $this;
    }

    /**
     * @param  array $serverParams
     * @return \Panadas\Kernel\HttpKernel
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
     * @return \Panadas\Kernel\HttpKernel
     */
    public function removeEnvParam($name)
    {
        $this->getEnvParams()->remove($name);

        return $this;
    }

    /**
     * @return \Panadas\Kernel\HttpKernel
     */
    public function removeAllEnvParams()
    {
        $this->getEnvParams()->removeAll();

        return $this;
    }

    /**
     * @param  string $name
     * @param  mixed  $value
     * @return \Panadas\Kernel\HttpKernel
     */
    public function setEnvParam($name, $value)
    {
        $this->getEnvParams()->set($name, $value);

        return $this;
    }

    /**
     * @param  array $envParams
     * @return \Panadas\Kernel\HttpKernel
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
}
