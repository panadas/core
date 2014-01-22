<?php
namespace Controller\Action;

class Redirect extends \Panadas\Controller\ActionAbstract
{

    protected function get(\Panadas\Request $request, \Panadas\ResponseAbstract $response)
    {
        $response
            ->setStatusCode($this->getArg("status_code", 302))
            ->setHeader("Location", $this->getArg("uri"));
    }

    protected function post(\Panadas\Request $request, \Panadas\ResponseAbstract $response)
    {
        return $this->get($request, $response);
    }

    protected function put(\Panadas\Request $request, \Panadas\ResponseAbstract $response)
    {
        return $this->get($request, $response);
    }

    protected function delete(\Panadas\Request $request, \Panadas\ResponseAbstract $response)
    {
        return $this->get($request, $response);
    }

}
