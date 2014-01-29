<?php
namespace Panadas\ArrayStore;

class ListArrayStore extends \Panadas\ArrayStore\AbstractArrayStore
{

    /**
     * @param  mixed $value
     * @return boolean
     */
    public function has($value)
    {
        return (null !== $this->indexOf($value));
    }

    /**
     * @param  mixed $value
     * @return integer
     */
    public function indexOf($value)
    {
        $index = array_search($value, $this->getAll());
        if (false === $index) {
            return null;
        }

        return $index;
    }

    /**
     * @param  mixed   $value
     * @return \Panadas\ArrayStore\ListArrayStore
     */
    public function prepend($value)
    {
        array_unshift($this->params, $value);

        return $this;
    }

    /**
     * @param  mixed   $value
     * @return \Panadas\ArrayStore\ListArrayStore
     */
    public function append($value)
    {
        $this->params[] = $value;

        return $this;
    }

    /**
     * @return mixed
     */
    public function shift()
    {
        return array_shift($this->params);
    }

    /**
     * @return mixed
     */
    public function pop()
    {
        return array_pop($this->params);
    }

    /**
     * @param  mixed $value
     * @return \Panadas\ArrayStore\ListArrayStore
     */
    public function remove($value)
    {
        $index = $this->indexOf($value);
        if (null !== $index) {
            unset($index);
        }

        return $this;
    }

    /**
     * @return \Panadas\ArrayStore\ListArrayStore
     */
    public function removeAll()
    {
        while ($this->hasAny()) {
            $this->pop();
        }

        return $this;
    }

    /**
     * @param  array $params
     * @return \Panadas\ArrayStore\ListArrayStore
     */
    public function replace(array $params)
    {
        $this->removeAll();

        array_walk($params, function ($value) {
        	$this->append($name);
        });

        return $this;
    }
}
