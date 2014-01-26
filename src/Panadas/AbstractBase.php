<?php
namespace Panadas;

abstract class AbstractBase implements \JsonSerializable, \Serializable
{

    /**
     * An empty constructor allows descendants to always call parent::__construct().
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
     * @see \JsonSerializable::jsonSerialize()
     * @return array
     */
    public function jsonSerialize()
    {
        return $this->__toArray();
    }

    /**
     * @see \Serializable::serialize()
     * @return string
     */
    public function serialize()
    {
        return serialize($this->__toArray());
    }

    /**
     * @see \Serializable::unserialize()
     * @param  string $serialized
     * @return \Panadas\AbstractBase
     */
    public function unserialize($serialized)
    {
        foreach (unserialize($serialized) as $key => $value) {
            $this->$key = $value;
        }

        return $this;
    }

}
