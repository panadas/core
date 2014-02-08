<?php
namespace Panadas\Framework;

use Panadas\DataStructure\Hash;
use Panadas\HttpMessage\Request;

abstract class AbstractAction extends AbstractApplicationAware
{

    private $args;

    abstract public function handle(Request $request);

    public function __construct(Application $application, array $args = [])
    {
        parent::__construct($application);

        $this->setArgs(new Hash($args));
    }

    public function getArgs()
    {
        return $this->args;
    }

    protected function setArgs(Hash $args)
    {
        $this->args = $args;

        return $this;
    }
}
