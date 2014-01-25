<?php
namespace Panadas;

abstract class AbstractBase implements \JsonSerializable
{

    /**
     * Empty constructor allows descendants to call a parent constructor without error.
     */
    public function __construct()
    {
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return "";
    }

    /**
     * @return array
     */
    public function __toArray()
    {
        return [];
    }

    /**
     * @see JsonSerializable::jsonSerialize()
     * @return array
     */
    public function jsonSerialize()
    {
        return $this->__toArray();
    }

}
