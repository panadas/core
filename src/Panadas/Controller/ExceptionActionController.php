<?php
namespace Panadas\Controller;

class ExceptionActionController extends \Panadas\Controller\AbstractActionController implements
    \Panadas\Error\ExceptionProcessorInterface
{

    protected function get(\Panadas\Http\Request $request)
    {
        $exception = $this->getArg("exception");

        if (!$exception instanceof \Exception) {
            throw new \RuntimeException("An instance of \Exception is required");
        }

        return static::process($this->getKernel(), $exception);
    }

    protected function post(\Panadas\Http\Request $request)
    {
        return $this->get($request);
    }

    protected function put(\Panadas\Http\Request $request)
    {
        return $this->get($request);
    }

    protected function delete(\Panadas\Http\Request $request)
    {
        return $this->get($request);
    }

    public static function process(\Panadas\Kernel\Kernel $kernel, \Exception $exception)
    {
        $logger = $kernel->getServiceContainer()->get("logger", false);
        if (null !== $logger) {
            $logger->critical($exception->getMessage(), ["exception" => $exception]);
        }

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
            ->setContent($content);
    }

}
