<?php
namespace Controller\Action;

class Exception extends \Controller\Action\HttpError
{

    protected function get(\Panadas\Http\Request $request)
    {
        $kernel = $this->getKernel();

        $exception = $this->getArg("exception");

        if ( ! $exception instanceof \Exception) {
            throw new \RuntimeException("An instance of \Exception is required");
        }

        $detail = "[" . get_class($exception) . "] {$exception->getMessage()} in {$exception->getFile()}:{$exception->getLine()}";

        $logger = $kernel->getServiceContainer()->get("logger", false);
        if (null !== $logger) {
            $logger->error($detail);
        }

        if ( ! $kernel->isDebugMode()) {
            return $kernel->error500();
        }

        return (new \Panadas\Http\Response\Html($kernel))
            ->setStatusCode($this->getArg("status_code", 500))
            ->setContent("<pre>" . htmlspecialchars($detail) . "</pre>");
    }

}
