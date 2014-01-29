<?php
namespace Panadas;

class ParamContainer extends \Panadas\AbstractBase
{

    private $params = [];

    /**
     * @param array $params
     */
    public function __construct(array $params = [])
    {
        $this->replace($params);
    }

    /**
     * @param  string $name
     * @param  mixed  $default
     * @return mixed
     */
    public function get($name, $default = null)
    {
        if ($this->has($name)) {
            return $this->params[$name];
        }

        if (is_callable($default)) {
            return $default($name);
        }

        return $default;
    }

    /**
     * @return array
     */
    public function getAll()
    {
        return $this->params;
    }

    /**
     * @return array
     */
    public function getNames()
    {
        return array_keys($this->getAll());
    }

    /**
     * @param  string $name
     * @return boolean
     */
    public function has($name)
    {
        return array_key_exists($name, $this->getAll());
    }

    /**
     * @return boolean
     */
    public function hasAny()
    {
        return (count($this->getAll()) > 0);
    }

    /**
     * @param  string $name
     * @return \Panadas\ParamContainer
     */
    public function remove($name)
    {
        if ($this->has($name)) {
            unset($this->params[$name]);
        }

        return $this;
    }

    /**
     * @param  array $names
     * @return \Panadas\ParamContainer
     */
    public function removeMany(array $names)
    {
        foreach ($names as $name) {
            $this->remove($name);
        }

        return $this;
    }

    /**
     * @return \Panadas\ParamContainer
     */
    public function removeAll()
    {
        return $this->removeMany($this->getNames());
    }

    /**
     * @param  string $name
     * @param  mixed  $value
     * @return \Panadas\ParamContainer
     */
    public function set($name, $value)
    {
        $this->params[$name] = $value;

        return $this;
    }

    /**
     * @param  array $params
     * @return \Panadas\ParamContainer
     */
    public function setMany(array $params)
    {
        foreach ($params as $name => $value) {
            $this->set($name, $value);
        }

        return $this;
    }

    /**
     * @param  array $params
     * @return \Panadas\ParamContainer
     */
    public function replace(array $params)
    {
        return $this->removeAll()->setMany($params);
    }
}
