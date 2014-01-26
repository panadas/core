<?php
namespace Panadas\Service;

class Container extends \Panadas\Http\AbstractKernelAware
{

    private $services = [];

    /**
     * @param  string $id
     * @param  \Panadas\Service\ServiceInterface $service
     * @return \Panadas\Service\Container
     */
    public function add($id, \Panadas\Service\ServiceInterface $service)
    {
        $this->services[$id] = $service;

        $this->getKernel()->addSubscriber($service);

        return $this;
    }

    /**
     * @param  array $services
     * @return \Panadas\Service\Container
     */
    public function addMany(array $services)
    {
        foreach ($services as $id => $service) {
            $this->add($id, $service);
        }

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
        if ( ! $this->has($id)) {

            if ($required) {
                throw new \RuntimeException("Service \"{$id}\" is required");
            }

            return null;

        }

        return $this->services[$id];
    }

    /**
     * @return array
     */
    public function getAll()
    {
        return $this->services;
    }

    /**
     * @return array
     */
    public function getIds()
    {
        return array_keys($this->getAll());
    }

    /**
     * @param  string $id
     * @return boolean
     */
    public function has($id)
    {
        return array_key_exists($id, $this->getAll());
    }

    /**
     * @return boolean
     */
    public function hasAny()
    {
        return (count($this->getAll()) > 0);
    }

    /**
     * @param  string $id
     * @return \Panadas\Service\Container
     */
    public function remove($id)
    {
        if ($this->has($id)) {
            $this->getKernel()->removeSubscriber($this->services[$id]);
            unset($this->services[$id]);
        }

        return $this;
    }

    /**
     * @param  array $ids
     * @return \Panadas\Service\Container
     */
    public function removeMany(array $ids)
    {
        foreach ($ids as $id) {
            $this->remove($id);
        }

        return $this;
    }

    /**
     * @return \Panadas\Service\Container
     */
    public function removeAll()
    {
        return $this->removeMany($this->getIds());
    }

    /**
     * @param  array $services
     * @return \Panadas\Service\Container
     */
    public function replace(array $services)
    {
        return $this->removeAll()->addMany($services);
    }

}
