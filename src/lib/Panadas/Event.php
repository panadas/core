<?php
namespace Panadas;

class Event extends \Panadas\AppHostAbstract
{

    private $name;
    private $params = [];
    private $stopped;

    public function __construct(\Panadas\App $app, $name, array $params = [])
    {
        parent::__construct($app);

        $this
            ->setStopped(false)
            ->setName($name)
            ->replace($params);
    }

    public function __toString()
    {
        return parent::__toString() . " ({$this->getName()})";
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

    public function get($name, $default = null)
    {
        return $this->has($name) ? $this->params[$name] : $default;
    }

    public function getAll()
    {
        return $this->params;
    }

    public function getNames()
    {
        return array_keys($this->getAll());
    }

    public function has($name)
    {
        return array_key_exists($name, $this->getAll());
    }

    public function hasAny()
    {
        return (count($this->getAll()) > 0);
    }

    public function set($name, $value)
    {
        $this->params[$name] = $value;

        return $this;
    }

    public function setMany(array $params = [])
    {
        foreach ($params as $name => $value) {
            $this->set($name, $value);
        }

        return $this;
    }

    protected function remove($name)
    {
        if ($this->has($name)) {
            unset($this->params[$name]);
        }

        return $this;
    }

    protected function removeMany(array $names)
    {
        foreach ($names as $name) {
            $this->remove($name);
        }

        return $this;
    }

    protected function removeAll()
    {
        return $this->removeMany($this->getNames());
    }

    protected function replace(array $params)
    {
        return $this->removeAll()->setMany($params);
    }

    public function isStopped()
    {
        return $this->stopped;
    }

    public function setStopped($stopped)
    {
        $this->stopped = (bool) $stopped;

        return $this;
    }

    public function stop()
    {
        return $this->setStopped(true);
    }

}
