<?php
namespace Panadas\Error;

class ExceptionHandler extends \Panadas\Kernel\AbstractKernelAware
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
        $logger = $this->getKernel()->getServiceContainer()->get("logger", false);
        if (null !== $logger) {
            $logger->critical($exception->getMessage(), ["exception" => $exception]);
        }

        $this->send($exception);
    }

    /**
     * @param  \Exception $exception
     * @return \Panadas\Http\Response
     */
    protected function send(\Exception $exception)
    {
        $kernel = $this->getKernel();
        $response = \Panadas\Http\DecoratedHtmlResponse::create($kernel);

        if (!$kernel->isDebugMode()) {

            $content = <<<CONTENT
                <div class="jumbotron">
                    <h1>Error</h1>
                    <p>Sorry, we are unable to process your request right now. Please try again later.</p>
                </div>
CONTENT;

        } else {

            $loader = $kernel->getLoader();
            $path = $exception->getFile();

            try {
                $path = $loader->getRelativePath($path);
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
                        $arg = \Panadas\Util\Php::toString($arg);
                    });

                    $line["args"] = implode(", ", $line["args"]);

                } else {
                    $line["args"] = null;
                }

                if (array_key_exists("file", $line)) {

                    try {
                        $line["file"] = $loader->getRelativePath($line["file"]);
                    } catch (\Exception $ignore) {
                    }

                } else {
                    $line["file"] = null;
                }

                if (!array_key_exists("line", $line)) {
                    $line["line"] = null;
                }


                $traceContent .= sprintf(
                    "
                        <tr>
                            <td><span class=\"badge\"><small>%d</small></span></td>
                            <td>
                                <small>
                                    <kbd>%s%s%s(<span class=\"text-muted\">%s</span>)</kbd>
                                    <br>
                                    <code>%s:%d</code>
                                </small>
                            </td>
                        </tr>
                    ",
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
            ->decorate($content)
            ->send();
    }
}
