<?php
namespace Panadas\Error;

class ExceptionHandler extends \Panadas\Http\AbstractKernelAware
{

    /**
     * @return \Panadas\Error\ExceptionHandler
     */
    public function register()
    {
        set_exception_handler([$this, "handle"]);

        return $this;
    }

    /**
     * @param \Exception $exception
     */
    public function handle(\Exception $exception)
    {
        if ($this->getKernel()->isHandling()) {
            $response = $this->exception($exception);
        } else {
            $response = $this->createResponse($exception);
        }

        $response->send();

        return;
    }

    /**
     * @param  \Exception $exception
     * @return \Panadas\Http\Response\Html
     */
    protected function createResponse(\Exception $exception)
    {
        $kernel = $this->getKernel();

        $title = "Error - " . htmlspecialchars($kernel->getName());

        if ($kernel->isDebugMode()) {
            $body = "<h1>" . htmlspecialchars(get_class($exception)) . "</h1>";
            $body .= "<pre>" . htmlspecialchars($exception->getMessage()) . "</pre>";
            $body .= "<dl>";
            $body .= "<dt>File:</dt>";
            $body .= "<dd><code>" . htmlspecialchars($exception->getFile()) . "</code></dd>";
            $body .= "<dt>Line:</dt>";
            $body .= "<dd><code>" . htmlspecialchars($exception->getLine()) . "</code></dd>";
            $body .= "<dt>Trace:</dt>";
            $body .= "<dd><pre>" . htmlspecialchars($exception->getTraceAsString()) . "</pre></dd>";
            $body .= "</dl>";
        } else {
            $body = "<h1>Server Error</h1>";
            $body .= "<p>Sorry, we are unable to process your request right now. Please try again later.</p>";
        }

        return (new \Panadas\Http\Response\Html($kernel))
            ->setStatusCode(500)
            ->setContent("<!DOCTYPE html><html><head><title>{$title}</title></head><body>{$body}</body></html>");
    }

}
