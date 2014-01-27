<?php
namespace Controller\Action;

class Redirect extends \Panadas\Controller\AbstractActionController
{

    protected function get(\Panadas\Http\Request $request)
    {
        return (new \Panadas\Http\Response($this->getKernel()))
            ->setStatusCode($this->getArg("status_code", 302))
            ->setHeader("Location", $this->getArg("uri"));
    }

    protected function post(\Panadas\Http\Request $request)
    {
        return $this->get($request);
    }

    protected function put(\Panadas\Http\Request $request)
    {
        return $this->get($request);
    }

    protected function delete(\Panadas\Http\Request $request)
    {
        return $this->get($request);
    }

}
