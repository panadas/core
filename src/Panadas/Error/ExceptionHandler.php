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
        $esc = function ($string) {
            return htmlspecialchars($string);
        };

        $kernel = $this->getKernel();

        $html = "<!DOCTYPE html>";
        $html .= "<html lang=\"en\">";
        $html .= "<head>";
        $html .= "<meta charset=\"UTF-8\">";
        $html .= "<meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">";
        $html .= "<link rel=\"stylesheet\" href=\"//netdna.bootstrapcdn.com/bootstrap/3.0.3/css/bootstrap.min.css\">";
        $html .= "<title>Error - {$esc($kernel->getName())}</title>";
        $html .= "<style>";
        $html .= "body {padding-top: 60px}";
        $html .= "</style>";
        $html .= "</head>";
        $html .= "<body>";
        $html .= "<div class=\"container\">";

        if ($kernel->isDebugMode()) {

            $html .= "<h1>Fatal Exception</h1>";
            $html .= "<div class=\"alert alert-danger\">";
            $html .= "<kbd>{$esc($exception->getMessage())}</kbd>";
            $html .= "</div>";
            $html .= "<dl class=\"dl-horizontal\">";
            $html .= "<dt>File</dt>";
            $html .= "<dd><kbd>{$esc($exception->getFile())}:{$esc($exception->getLine())}</kbd></dd>";
            $html .= "<dt>Type</dt>";
            $html .= "<dd><kbd>" . $esc(get_class($exception)) . " (Code {$esc($exception->getCode())})</kbd></dd>";
            $html .= "<dt>Trace</dt>";
            $html .= "<dd><pre>{$esc($exception->getTraceAsString())}</pre></dd>";
            $html .= "</dl>";

        } else {

            $html .= "<div class=\"jumbotron\">";
            $html .= "<h1>Server Error</h1>";
            $html .= "<p>Sorry, we are unable to process your request right now. Please try again later.</p>";
            $html .= "</div>";

        }

        $html .= "</div>";
        $html .= "</body>";
        $html .= "</html>";

        return (new \Panadas\Http\HtmlResponse($kernel))
            ->setStatusCode(500)
            ->setContent($html);
    }
}
