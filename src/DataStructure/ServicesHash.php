<?php
namespace Panadas\Framework\DataStructure;

use Panadas\DataStructure\Hash;
use Panadas\EventManager\SubscriberInterface;
use Panadas\Framework\Application;
use Panadas\Framework\ApplicationAwareInterface;

class ServicesHash extends Hash implements ApplicationAwareInterface
{

    private $application;

    public function __construct(Application $application, array $services = [])
    {
        $this->setApplication($application);

        parent::__construct($services);
    }

    public function getApplication()
    {
        return $this->application;
    }

    protected function setApplication(Application $application)
    {
        $this->application = $application;

        return $this;
    }

    public function set($key, $value)
    {
        parent::set($key, $value);

        if ($value instanceof SubscriberInterface) {
            $this->getApplication()->attach($value);
        }

        return $this;
    }

    public function remove($key)
    {
        $service = $this->get($key);

        if ($service instanceof SubscriberInterface) {
            $this->getApplication()->detach($service);
        }

        return parent::remove($key);
    }
}
