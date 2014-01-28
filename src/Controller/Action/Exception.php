<?php
namespace Controller\Action;

class Exception extends \Controller\Action\HttpError
{

    protected function get(\Panadas\Http\Request $request)
    {
        $kernel = $this->getKernel();
        $sc = $kernel->getServiceContainer();

        $exception = $this->getArg("exception");

        if ( ! $exception instanceof \Exception) {
            throw new \RuntimeException("An instance of \Exception is required");
        }

        if ($sc->has("logger")) {
            $sc->get("logger")->critical($exception->getMessage(), ["exception" => $exception]);
        }

        if ( ! $kernel->isDebugMode()) {
            return $kernel->error500();
        }

        $exception_data = [
            "type" => get_class($exception),
            "code" => $exception->getCode(),
            "message" => $exception->getMessage(),
            "file" => $exception->getFile(),
            "line" => $exception->getLine(),
            "trace" => $exception->getTraceAsString()
        ];

        if ($request->isAjax()) {

            $response = (new \Panadas\Http\JsonResponse($kernel))
                ->setContent($exception_data);

        } elseif ($kernel->getServiceContainer()->has("twig")) {

            $response = (new \Panadas\TwigModule\Http\TwigResponse($kernel))
                ->set("exception", $exception_data);

        } else {

            $response = (new \Panadas\Http\HtmlResponse($kernel))
                ->setContent("<pre>[{$exception_data["type"]}] {$exception_data["message"]} in {$exception_data["file"]}:{$exception_data["line"]}</pre>");

        }

        $response->setStatusCode($this->getArg("status_code", 500));

        if ($response instanceof \Panadas\TwigModule\Http\TwigResponse) {
            $response->render("Exception.twig");
        }

        return $response;
    }

}
