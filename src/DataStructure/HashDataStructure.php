<?php
namespace Panadas\DataStructure;

class HashDataStructure extends \Panadas\DataStructure\AbstractDataStructure
{

    /**
     * @param  string $name
     * @param  mixed  $default
     * @return mixed
     */
    protected function handleDefault($name, $default = null)
    {
        if (is_callable($default)) {
            return $default($name);
        }

        return $default;
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

        return $this->handleDefault($name, $default);
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
     * @param  string $name
     * @return \Panadas\DataStructure\HashDataStructure
     */
    public function remove($name)
    {
        if ($this->has($name)) {
            unset($this->params[$name]);
        }

        return $this;
    }

    /**
     * @return \Panadas\DataStructure\AbstractDataStructure
     */
    public function removeAll()
    {
        $names = $this->getNames();
        array_walk($names, [$this, "remove"]);

        return $this;
    }

    /**
     * @param  string  $name
     * @param  mixed   $value
     * @param  boolean $replace
     * @return \Panadas\DataStructure\HashDataStructure
     */
    public function set($name, $value, $replace = true)
    {
        if ($this->has($name)) {

            if (!$replace) {
                return $this;
            }

            $this->remove($name);

        }

        $this->params[$name] = $value;

        return $this;
    }

    /**
     * @param  array $params
     * @return \Panadas\DataStructure\HashDataStructure
     */
    public function replace(array $params)
    {
        $this->removeAll();

        $callback = function ($value, $name) {
            $this->set($name, $value);
        };

        array_walk($params, $callback);

        return $this;
    }
}
