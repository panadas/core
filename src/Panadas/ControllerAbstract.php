<?php
namespace Panadas;

abstract class ControllerAbstract extends \Panadas\AppHostAbstract implements \Panadas\ControllerInterface
{

    private $name;
    private $args = [];

    public function __construct(\Panadas\App $app, $name, array $args = [])
    {
        parent::__construct($app);

        $this
            ->setName($name)
            ->replaceArgs($args);
    }

    public function getName()
    {
        return $this->name;
    }

    protected function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    public function getArg($name, $default = null)
    {
        return $this->hasArg($name) ? $this->args[$name] : $default;
    }

    public function getAllArgs()
    {
        return $this->args;
    }

    public function getArgNames()
    {
        return array_keys($this->getAllArgs());
    }

    public function hasArg($name)
    {
        return array_key_exists($name, $this->getAllArgs());
    }

    public function hasAnyArgs()
    {
        return (count($this->getAllArgs()) > 0);
    }

    public function removeArg($name)
    {
        if ($this->hasArg($name)) {
            unset($this->args[$name]);
        }

        return $this;
    }

    public function removeManyArgs(array $names)
    {
        foreach ($names as $name) {
            $this->removeArg($name);
        }

        return $this;
    }

    public function removeAllArgs()
    {
        return $this->removeManyArgs($this->getArgNames());
    }

    public function replaceArgs(array $args)
    {
        return $this->removeAllArgs()->setManyArgs($args);
    }

    public function setArg($name, $value)
    {
        $this->args[$name] = $value;

        return $this;
    }

    public function setManyArgs(array $args)
    {
        foreach ($args as $name => $value) {
            $this->setArg($name, $value);
        }

        return $this;
    }

}
