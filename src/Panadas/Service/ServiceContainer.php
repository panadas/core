<?php
namespace Panadas\Service;

class ServiceContainer extends \Panadas\Kernel\AbstractKernelAware
{

    // TODO: Use custom array store for event subscription handling
    private $services;

    /**
     * @param \Panadas\Kernel\Kernel $kernel
     * @param array                  $services
     */
    public function __construct(\Panadas\Kernel\Kernel $kernel, array $services = [])
    {
        parent::__construct($kernel);

        $this->setServices(new \Panadas\DataStructure\HashDataStructure($services));
    }

    /**
     * @return \Panadas\DataStructure\HashDataStructure
     */
    protected function getServices()
    {
        return $this->services;
    }

    /**
     * @param  \Panadas\DataStructure\HashDataStructure $services
     * @return \Panadas\Service\ServiceContainer
     */
    protected function setServices(\Panadas\DataStructure\HashDataStructure $services)
    {
        $this->services = $services;

        return $this;
    }

    /**
     * @param  string  $id
     * @param  boolean $required
     * @throws \RuntimeException
     * @return \Panadas\Service\ServiceInterface
     */
    public function get($id, $required = true)
    {
        $service = $this->getServices()->get($id);

        if ((null === $service) && $required) {
            throw new \RuntimeException("Service \"{$id}\" is required");
        }

        return $service;
    }

    /**
     * @return array
     */
    public function getAll()
    {
        return $this->getServices()->getAll();
    }

    /**
     * @return array
     */
    public function getIds()
    {
        return $this->getServices()->getNames();
    }

    /**
     * @param  string $id
     * @return boolean
     */
    public function has($id)
    {
        return $this->getServices()->has($id);
    }

    /**
     * @return boolean
     */
    public function hasAny()
    {
        return $this->getServices()->hasAny();
    }

    /**
     * @param  string $id
     * @return \Panadas\Service\ServiceContainer
     */
    public function remove($id)
    {
        if ($this->getServices()->has($id)) {
            $this->getKernel()->removeSubscriber(
                $this->getServices()->get($id)
            );
        }
        $this->getServices()->remove($id);

        return $this;
    }

    /**
     * @return \Panadas\Service\ServiceContainer
     */
    public function removeAll()
    {
        $this->getServices()->removeAll();

        return $this;
    }

    /**
     * @param  string $id
     * @param  \Panadas\Service\ServiceInterface $service
     * @return \Panadas\Service\ServiceContainer
     */
    public function set($id, \Panadas\Service\ServiceInterface $service)
    {
        $this->getServices()->set($id, $service);
        $this->getKernel()->addSubscriber($service);

        return $this;
    }

    /**
     * @param  array $services
     * @return \Panadas\Service\ServiceContainer
     */
    public function replace(array $services)
    {
        $this->getServices()->replace($services);

        return $this;
    }
}
