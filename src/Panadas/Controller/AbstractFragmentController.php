<?php
namespace Panadas\Controller;

abstract class AbstractFragmentController extends \Panadas\Controller\AbstractController
{

    public function handle(\Panadas\Http\Request $request)
    {
        // TODO
    }

    public static function getClassName($name)
    {
        return "Controller\Fragment\\{$name}";
    }

}