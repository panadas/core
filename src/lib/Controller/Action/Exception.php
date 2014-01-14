<?php
namespace Controller\Action;

class Exception extends \Controller\Action\HttpError
{

    protected function get(\Panadas\Request $request, \Panadas\ResponseAbstract $response)
    {
        $app = $this->getApp();

        $exception = $this->getArg("exception");

        if ( ! $exception instanceof \Exception) {
            throw new \RuntimeException("An instance of \Exception is required");
        }

        $type = get_class($exception);

        $logger = $app->getServiceContainer()->get("logger", false);
        if (!is_null($logger)) {
            $logger->error("[{$type}] {$exception->getMessage()} in {$exception->getFile()}:{$exception->getLine()}");
        }

        if (!$app->isDebugMode()) {
            return $app->error500();
        }

        $status_code = $this->getArg("status_code", 500);

        $response
            ->setStatusCode($status_code)
            ->set(
                "exception",
                [
                    "type" => $type,
                    "code" => $exception->getCode(),
                    "message" => $exception->getMessage(),
                    "file" => $exception->getFile(),
                    "line" => $exception->getLine(),
                    "stacktrace" => $exception->getTraceAsString(),
                ]
            );

        if ($response instanceof \Panadas\Response\HtmlAbstract) {
            $response->setTitle("Exception");
        }
    }

}
