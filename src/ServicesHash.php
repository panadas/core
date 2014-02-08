<?php
namespace Panadas\Framework;

use Panadas\DataStructure\Hash;
use Panadas\EventManager\SubscriberInterface;

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
        $application = $this->getApplication();

        if (is_callable($value)) {
            $value = $value($application);
        }

        if (!$value instanceof ServiceInterface) {
            throw new \RuntimeException("Cannot create service instance: {$key}");
        }

        if ($value instanceof SubscriberInterface) {
            $application->subscribe($value);
        }

        return parent::set($key, $value);
    }

    public function remove($key)
    {
        $service = $this->get($key);

        if ($service instanceof SubscriberInterface) {
            $this->getApplication()->unsubscribe($service);
        }

        return parent::remove($key);
    }
}
