<?php
namespace Panadas\Controller;

abstract class FragmentAbstract extends \Panadas\ControllerAbstract
{

    public abstract function embed(\Panadas\Request $request, \Panadas\ResponseAbstract $response);

    public static function getClassName($name)
    {
        if (empty($name)) {
            throw new \Panadas\InvalidArgument("name", $name, "A value is required");
        }

        return "Controller\Fragment\\{$name}";
    }

}
