<?php
namespace Panadas\DataStructure;

class HashDataStructure extends \Panadas\DataStructure\AbstractDataStructure
{

    private $caseSensitive;

    /**
     * @param array   $params
     * @param boolean $caseSensitive
     */
    public function __construct(array $params = [], $caseSensitive = true)
    {
        parent::__construct($params);

        $this->setCaseSensitive($caseSensitive);
    }

    /**
     * @return boolean
     */
    public function isCaseSensitive()
    {
        return $this->caseSensitive;
    }

    /**
     * @param  boolean $caseSensitive
     * @return \Panadas\DataStructure\HashDataStructure
     */
    protected function setCaseSensitive($caseSensitive)
    {
        $this->caseSensitive = (bool) $caseSensitive;

        return $this;
    }

    /**
     * @param  string $name
     * @param  mixed  $default
     * @return mixed
     */
    public function get($name, $default = null)
    {
        $storedName = $this->getStoredName($name);
        if (null !== $storedName) {
            return $this->params[$storedName];
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
        return (null !== $this->getStoredName($name));
    }

    /**
     * @param  string $name
     * @return \Panadas\DataStructure\HashDataStructure
     */
    public function remove($name)
    {
        $storedName = $this->getStoredName($name);
        if (null !== $storedName) {
            unset($this->params[$storedName]);
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

        array_walk(
            $params,
            function ($value, $name) {
                $this->set($name, $value);
            }
        );

        return $this;
    }

    /**
     * @param  string $name
     * @return string
     */
    public function getStoredName($name)
    {
        $params = $this->getAll();

        if ($this->isCaseSensitive()) {
            return array_key_exists($name, $params) ? $name : null;
        }

        foreach ($params as $key => $value) {
            if (0 === strcasecmp($key, $name)) {
                return $key;
            }
        }

        return null;
    }
}
