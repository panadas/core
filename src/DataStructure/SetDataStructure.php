<?php
namespace Panadas\DataStructure;

class SetDataStructure extends \Panadas\DataStructure\ListDataStructure
{

    /**
     * @param  mixed   $value
     * @return \Panadas\DataStructure\SetDataStructure
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
     * @return \Panadas\DataStructure\SetDataStructure
     */
    public function append($value)
    {
        if (!$this->has($value)) {
            return parent::append($value);
        }

        return $this;
    }
}
