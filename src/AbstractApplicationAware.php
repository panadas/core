<?php
namespace Panadas\Framework;

abstract class AbstractApplicationAware implements ApplicationAwareInterface
{

    use ApplicationAwareTrait;

    public function __construct(Application $application)
    {
        $this->setApplication($application);
    }
}
