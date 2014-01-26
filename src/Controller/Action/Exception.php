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

        $type = get_class($exception);

        $logger = $kernel->getServiceContainer()->get("logger", false);
        if (null !== $logger) {
            $logger->error("[{$type}] {$exception->getMessage()} in {$exception->getFile()}:{$exception->getLine()}");
        }

        if ( ! $kernel->isDebugMode()) {
            return $kernel->error500();
        }

        $status_code = $this->getArg("status_code", 500);

        return (new \Panadas\Http\Response($kernel))
            ->setStatusCode($status_code)
            ->setBody("EXCEPTION: {$exception->getMessage()} in {$exception->getFile()}:{$exception->getLine()}");
    }

}
