<?php
namespace Panadas\Framework;

use Panadas\Framework\HttpMessage\HtmlResponse;
use Panadas\Util\Php;

class ExceptionHandler extends AbstractApplicationAware
{

    public function register()
    {
        set_exception_handler([$this, "handle"]);

        return $this;
    }

    public function handle(\Exception $exception)
    {
        $logger = $this->getApplication()->getServices()->get("logger");
        if (null !== $logger) {
            $logger->critical($exception->getMessage(), ["exception" => $exception]);
        }

        $this->send($exception);
    }

    protected function send(\Exception $exception)
    {
        $application = $this->getApplication();
        $response = new HtmlResponse($application);

        if (!$application->isDebugMode()) {

            $content = '
                <div class="jumbotron">
                    <h1>Error</h1>
                    <p>Sorry, we are unable to process your request right now. Please try again later.</p>
                </div>
            ';

        } else {

            $path = $exception->getFile();

            try {
                $path = $application->getRelativePath($path);
            } catch (\Exception $ignore) {
            }

            $trace = $exception->getTrace();
            $index = count($trace);

            $traceContent = null;

            foreach ($trace as $line) {

                if (!array_key_exists("class", $line)) {
                    $line["class"] = null;
                }

                if (!array_key_exists("type", $line)) {
                    $line["type"] = null;
                }

                if (!array_key_exists("function", $line)) {
                    $line["function"] = null;
                }

                if (array_key_exists("args", $line)) {

                    array_walk($line["args"], function (&$arg) {
                        $arg = Php::toString($arg);
                    });

                    $line["args"] = implode(", ", $line["args"]);

                } else {
                    $line["args"] = null;
                }

                if (array_key_exists("file", $line)) {

                    try {
                        $line["file"] = $application->getRelativePath($line["file"]);
                    } catch (\Exception $ignore) {
                    }

                } else {
                    $line["file"] = null;
                }

                if (!array_key_exists("line", $line)) {
                    $line["line"] = null;
                }


                $traceContent .= sprintf(
                    '
                        <tr>
                            <td><span class="badge"><small>%d</small></span></td>
                            <td>
                                <small>
                                    <kbd>%s%s%s(<span class="text-muted">%s</span>)</kbd>
                                    <br>
                                    <code>%s:%d</code>
                                </small>
                            </td>
                        </tr>
                    ',
                    $response->esc($index),
                    $response->esc($line["class"]),
                    $response->esc($line["type"]),
                    $response->esc($line["function"]),
                    $response->esc($line["args"]),
                    $response->esc($line["file"]),
                    $response->esc($line["line"])
                );

                $index--;
            }

            $content = <<<CONTENT
                <div class="jumbotron">
                    <h1>Exception</h1>
                </div>
                <div class="alert alert-danger">
                    <kbd>{$response->esc($exception->getMessage())}</kbd>
                </div>
                <dl class="dl-horizontal">
                    <dt>File</dt>
                    <dd><kbd>{$response->esc($path)}:{$response->esc($exception->getLine())}</kbd></dd>
                    <dt>Type</dt>
                    <dd><kbd>{$response->esc(get_class($exception))}</kbd></dd>
                    <dt>Code</dt>
                    <dd><kbd>{$response->esc($exception->getCode())}</kbd></dd>
                </dl>
                <h3>Trace</h3>
                <table class="table table-striped">
                    {$traceContent}
                </table>
CONTENT;

        }

        return $response
            ->setStatusCode(500)
            ->render($content)
            ->send();
    }
}
