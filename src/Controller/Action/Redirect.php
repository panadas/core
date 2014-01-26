<?php
namespace Controller\Action;

class Redirect extends \Panadas\Controller\AbstractAction
{

    protected function get(\Panadas\Http\Request $request)
    {
        $response = new \Panadas\Http\Response($this->getKernel());

        $response
            ->setStatusCode($this->getArg("status_code", 302))
            ->setHeader("Location", $this->getArg("uri"));

        return $response;
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
