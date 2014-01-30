<?php
namespace Panadas\ArrayStore;

class HashArrayStore extends \Panadas\ArrayStore\AbstractArrayStore
{

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
     * @return \Panadas\ArrayStore\HashArrayStore
     */
    public function remove($name)
    {
        if ($this->has($name)) {
            unset($this->params[$name]);
        }

        return $this;
    }

    /**
     * @return \Panadas\ArrayStore\AbstractArrayStore
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
     * @return \Panadas\ArrayStore\HashArrayStore
     */
    public function set($name, $value, $replace = true)
    {
        if ($replace || !$this->has($name)) {
            $this->params[$name] = $value;
        }

        return $this;
    }

    /**
     * @param  array $params
     * @return \Panadas\ArrayStore\HashArrayStore
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
