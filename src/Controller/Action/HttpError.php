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

            $response = (new \Panadas\TwigModule\Http\TwigResponse($kernel, "HttpError.twig"))
                ->set("message", $message);

        } else {

            $response = (new \Panadas\Http\HtmlResponse($kernel))
                ->setContent("message", htmlspecialchars($message));

        }

        return $response
            ->setStatusCode($this->getArg("status_code"));
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
