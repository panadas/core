<?php
namespace Controller\Action;

class Homepage extends \Panadas\Controller\ActionAbstract
{

    protected function get(\Panadas\Request $request, \Panadas\ResponseAbstract $response)
    {
        if ($response instanceof \Panadas\Response\HtmlAbstract) {
            $response->setTitle("Welcome to {$this->getApp()->getName()}");
        }
    }

}
