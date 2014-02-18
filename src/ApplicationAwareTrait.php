<?php
namespace Panadas\Framework;

trait ApplicationAwareTrait
{

    private $application;

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
