<?php
namespace Panadas;

abstract class BaseAbstract implements \JsonSerializable
{

    public function __construct()
    {
    }

    public function __toString()
    {
        return "";
    }

    public function __toArray()
    {
        return [];
    }

    public function jsonSerialize()
    {
        return $this->__toArray();
    }

}
