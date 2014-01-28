<?php
namespace Controller\Action;

class HttpError extends \Panadas\Controller\AbstractActionController
{

    protected function get(\Panadas\Http\Request $request)
    {
        $kernel = $this->getKernel();

        $message = $this->getArg("message");

        if ($request->isAjax()) {

            $response = (new \Panadas\Http\JsonResponse($kernel))
                ->setContent("message", $message);

        } elseif ($kernel->getServiceContainer()->has("twig")) {

            $response = (new \Panadas\TwigModule\Http\TwigResponse($kernel))
                ->set("message", $message);

        } else {

            $response = (new \Panadas\Http\HtmlResponse($kernel))
                ->setContent("message", htmlspecialchars($message));

        }

        $response->setStatusCode($this->getArg("status_code"));

        if ($response instanceof \Panadas\TwigModule\Http\TwigResponse) {
            $response->render("HttpError.twig");
        }

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
