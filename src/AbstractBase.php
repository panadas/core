<?php
namespace Panadas;

abstract class AbstractBase
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
}
