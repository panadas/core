<?php
namespace Panadas\Controller;

class HttpErrorActionController extends \Panadas\Controller\AbstractActionController
{

    protected function get(\Panadas\Http\Request $request)
    {
        $response = \Panadas\Http\DecoratedHtmlResponse::create($this->getKernel());

        $statusCode = $this->getArg("statusCode");

        $title = "HTTP {$response->esc($statusCode)}";
        $title .= " <small>{$response->esc($response::getStatusMessage($statusCode))}</small>";

        $message = $this->getArg("message");
        if (null !== $message) {
            $message = "<div class=\"alert alert-danger\">{$response->esc($message)}</div>";
        }

        $content = <<<CONTENT
            <div class="jumbotron">
                <h1>{$title}</h1>
            </div>
            {$message}
CONTENT;

        return $response
            ->setStatusCode($statusCode)
            ->setContent($content);
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
}
