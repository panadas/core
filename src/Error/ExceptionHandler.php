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

            $path = $exception->getFile();

            try {
                $path = $kernel->getLoader()->getRelativePath($path);
            } catch (\Exception $ignore) {
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
                <h5>Trace</h5>
                <pre>{$response->esc($exception->getTraceAsString())}</pre>
CONTENT;

        }

        return $response
            ->setStatusCode(500)
            ->decorate($content)
            ->send();
    }
}
