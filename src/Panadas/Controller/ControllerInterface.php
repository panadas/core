<?php
namespace Panadas\Controller;

interface ControllerInterface
{

    /**
     * @param  string $name
     * @return string
     */
    public static function getClassName($name);
}
