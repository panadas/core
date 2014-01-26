<?php
namespace Controller\Action;

class HttpError extends \Panadas\Controller\AbstractAction
{

    protected function get(\Panadas\Http\Request $request)
    {
        $status_code = $this->getArg("status_code");

        $response
            ->setStatusCode($status_code)
            ->set("message", $this->getArg("message"));

        if ($response instanceof \Panadas\Http\Response\AbstractHtml) {
            $status_message = \Panadas\Util\Http::getStatusMessageFromCode($status_code);
            $response->setTitle("HTTP Error {$status_code} ({$status_message})");
        }
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
