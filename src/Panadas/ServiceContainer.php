<?php
namespace Panadas;

class ServiceContainer extends \Panadas\AppHostAbstract
{

    private $services = [];

    public function __toArray()
    {
        $services = [];

        foreach ($this->getAll() as $id => $service) {
            $services[$id] = $service->__toArray();
        }

        return (
            parent::__toArray()
            + [
                "services" => $services
            ]
        );
    }

    public function add($id, \Panadas\ServiceAbstract $service)
    {
        $this->services[$id] = $service;

        $this->getApp()->addSubscriber($service);

        return $this;
    }

    public function addMany(array $services)
    {
        foreach ($services as $id => $service) {
            $this->add($id, $service);
        }

        return $this;
    }

    public function get($id, $required = true)
    {
        if (!$this->has($id)) {

            if ($required) {
                throw new \RuntimeException("Service \"{$id}\" is required");
            }

            return null;

        }

        return $this->services[$id];
    }

    public function getAll()
    {
        return $this->services;
    }

    public function getIds()
    {
        return array_keys($this->getAll());
    }

    public function has($id)
    {
        return array_key_exists($id, $this->getAll());
    }

    public function hasAny()
    {
        return (count($this->getAll()) > 0);
    }

    public function remove($id)
    {
        if ($this->has($id)) {

            $this->getApp()->removeSubscriber($this->services[$id]);

            unset($this->services[$id]);

        }

        return $this;
    }

    public function removeMany(array $ids)
    {
        foreach ($ids as $id) {
            $this->remove($id);
        }

        return $this;
    }

    public function removeAll()
    {
        return $this->removeMany($this->getIds());
    }

    public function replace(array $services)
    {
        return $this->removeAll()->addMany($services);
    }

}
