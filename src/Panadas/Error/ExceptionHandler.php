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
        $kernel = $this->getKernel();

        if ($kernel->isHandling()) {

            $response = $kernel->exception($exception);

        } else {

            $logger = $kernel->getServiceContainer()->get("logger", false);

            if (null !== $logger) {
                $logger->critical($exception->getMessage(), ["exception" => $exception]);
            }

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
        $esc = function($string) {
            return htmlspecialchars($string);
        };

        $kernel = $this->getKernel();

        $content = "<!DOCTYPE html>";
        $content .= "<html lang=\"en\">";
        $content .= "<head>";
        $content .= "<meta charset=\"UTF-8\">";
        $content .= "<meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">";
        $content .= "<link rel=\"stylesheet\" href=\"//netdna.bootstrapcdn.com/bootstrap/3.0.3/css/bootstrap.min.css\">";
        $content .= "<title>Error - {$esc($kernel->getName())}</title>";
        $content .= "<style>";
        $content .= "body {padding-top: 60px}";
        $content .= "</style>";
        $content .= "</head>";
        $content .= "<body>";
        $content .= "<div class=\"container\">";

        if ($kernel->isDebugMode()) {

            $content .= "<h1>Fatal Exception</h1>";
            $content .= "<div class=\"alert alert-danger\">";
            $content .= "<kbd>{$esc($exception->getMessage())}</kbd>";
            $content .= "</div>";
            $content .= "<dl class=\"dl-horizontal\">";
            $content .= "<dt>File</dt>";
            $content .= "<dd><kbd>{$esc($exception->getFile())}:{$esc($exception->getLine())}</kbd></dd>";
            $content .= "<dt>Type</dt>";
            $content .= "<dd><kbd>" . $esc(get_class($exception)) . " (Code {$esc($exception->getCode())})</kbd></dd>";
            $content .= "<dt>Trace</dt>";
            $content .= "<dd><pre>{$esc($exception->getTraceAsString())}</pre></dd>";
            $content .= "</dl>";

        } else {

            $content .= "<div class=\"jumbotron\">";
            $content .= "<h1>Server Error</h1>";
            $content .= "<p>Sorry, we are unable to process your request right now. Please try again later.</p>";
            $content .= "</div>";

        }

        $content .= "</div>";
        $content .= "</body>";
        $content .= "</html>";

        return (new \Panadas\Http\HtmlResponse($kernel))
            ->setStatusCode(500)
            ->setContent($content);
    }

}
