<?php
namespace Panadas\Controller;

class HttpRedirectActionController extends \Panadas\Controller\AbstractActionController
{

    /**
     * @param  \Panadas\Http\Request $request
     * @return \Panadas\Http\Response
     */
    protected function get(\Panadas\Http\Request $request)
    {
        return (new \Panadas\Http\Response($this->getKernel()))
            ->setStatusCode($this->getArg("statusCode", 302))
            ->setHeader("Location", $this->getArg("uri"));
    }

    /**
     * @param  \Panadas\Http\Request $request
     * @return \Panadas\Http\Response
     */
    protected function post(\Panadas\Http\Request $request)
    {
        return $this->get($request);
    }

    /**
     * @param  \Panadas\Http\Request $request
     * @return \Panadas\Http\Response
     */
    protected function put(\Panadas\Http\Request $request)
    {
        return $this->get($request);
    }

    /**
     * @param  \Panadas\Http\Request $request
     * @return \Panadas\Http\Response
     */
    protected function delete(\Panadas\Http\Request $request)
    {
        return $this->get($request);
    }
}
