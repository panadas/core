<?php
namespace Panadas\Controller;

abstract class AbstractFragment extends \Panadas\Controller\AbstractController
{

    public abstract function embed(\Panadas\Http\Request $request);

    public static function getClassName($name)
    {
        if (empty($name)) {
            throw new \Panadas\InvalidArgument("name", $name, "A value is required");
        }

        return "Controller\Fragment\\{$name}";
    }

}
