<?php
namespace Panadas\Framework\Action;

use Panadas\HttpMessage\Request;
use Panadas\Framework\HttpMessage\HtmlResponse;

class HttpError extends AbstractAction
{

    protected function get(Request $request)
    {
        $response = new HtmlResponse($this->getApplication());

        $args = $this->getArgs();
        $statusCode = $args->get("statusCode");
        $message = $args->get("message");

        $title = "HTTP {$response->esc($statusCode)}";
        $title .= " <small>{$response->esc($response::getStatusMessage($statusCode))}</small>";

        if (null !== $message) {
            $message = "<div class=\"alert alert-danger\">{$response->esc($message)}</div>";
        }

        return $response
            ->setStatusCode($statusCode)
            ->render(
                '
                    <div class="jumbotron">
                        <h1>' . $title . '</h1>
                    </div>
                    ' . $message . '
                '
            );
    }

    protected function post(Request $request)
    {
        return $this->get($request);
    }

    protected function put(Request $request)
    {
        return $this->get($request);
    }

    protected function delete(Request $request)
    {
        return $this->get($request);
    }
}
