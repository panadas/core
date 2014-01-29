<?php
namespace Panadas\ArrayStore;

class SetArrayStore extends \Panadas\ArrayStore\ListArrayStore
{

    /**
     * @param  mixed   $value
     * @return \Panadas\ArrayStore\SetArrayStore
     */
    public function prepend($value)
    {
        if (!$this->has($value)) {
            return parent::prepend($value);
        }

        return $this;
    }

    /**
     * @param  mixed   $value
     * @return \Panadas\ArrayStore\SetArrayStore
     */
    public function append($value)
    {
        if (!$this->has($value)) {
            return parent::append($value);
        }

        return $this;
    }
}
