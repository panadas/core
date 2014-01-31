<?php
namespace Panadas\DataStructure;

class NoCaseHashDataStructure extends \Panadas\DataStructure\HashDataStructure
{

    /**
     * @param  string $name
     * @return string
     */
    public function getStoredName($name)
    {
        $name = mb_strtoupper($name);

        foreach ($this->getAll() as $key => $value) {
            if (mb_strtoupper($key) === $name) {
                return $key;
            }
        }

        return null;
    }

    /**
     * @param  string $name
     * @param  mixed  $default
     * @return mixed
     */
    public function get($name, $default = null)
    {
        if (parent::has($name)) {
            return $this->params[$name];
        }

        $storedName = $this->getStoredName($name);
        if (null !== $storedName) {
            return $this->params[$storedName];
        }

        return $this->handleDefault($name, $default);
    }

    /**
     * @param  string $name
     * @return boolean
     */
    public function has($name)
    {
        if (parent::has($name)) {
            return true;
        }

        return (null !== $this->getStoredName($name));
    }

    /**
     * @param  string $name
     * @return \Panadas\DataStructure\HashDataStructure
     */
    public function remove($name)
    {
        if (parent::has($name)) {
            unset($this->params[$name]);
        } else {
            $storedName = $this->getStoredName($name);
            if (null !== $storedName) {
                unset($this->params[$storedName]);
            }
        }

        return $this;
    }
}
