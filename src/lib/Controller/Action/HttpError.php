<?php
namespace Controller\Action;

class HttpError extends \Panadas\Controller\ActionAbstract
{

    protected function get(\Panadas\Request $request, \Panadas\ResponseAbstract $response)
    {
        $status_code = $this->getArg("status_code");

        $response
            ->setStatusCode($status_code)
            ->set("message", $this->getArg("message"));

        if ($response instanceof \Panadas\Response\HtmlAbstract) {
            $status_message = \Panadas\Util\Http::getStatusMessageFromCode($status_code);
            $response->setTitle("HTTP Error {$status_code} ({$status_message})");
        }
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
