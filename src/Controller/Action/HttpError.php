<?php
namespace Controller\Action;

class HttpError extends \Panadas\Controller\AbstractAction
{

    protected function get(\Panadas\Http\Request $request)
    {
        $kernel = $this->getKernel();

        $message = $this->getArg("message");

        if ($request->isAjax()) {

            $response = (new \Panadas\Http\Response\Json($kernel))
                ->setContent("message", $message);

        } elseif ($kernel->getServiceContainer()->has("twig")) {

            $response = (new \Panadas\TwigModule\Response($kernel, "HttpError.twig.html"))
                ->set("message", $message);

        } else {

            $response = (new \Panadas\Http\Response\Html($kernel))
                ->setContent("message", htmlspecialchars($message));

        }

        $response->setStatusCode($this->getArg("status_code"));

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
