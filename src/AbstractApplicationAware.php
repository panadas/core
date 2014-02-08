<?php
namespace Panadas\Framework;

abstract class AbstractApplicationAware implements ApplicationAwareInterface
{

    private $application;

    public function __construct(Application $application)
    {
        $this->setApplication($application);
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
}
