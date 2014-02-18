<?php
namespace Panadas\Framework\Action;

use Panadas\HttpMessage\Request;
use Panadas\Framework\HttpMessage\HtmlResponse;

class Redirect extends AbstractAction
{

    protected function get(Request $request)
    {
        $args = $this->getArgs();

        $uri = $args->get("uri");
        if (null === $uri) {
            throw new \InvalidArgumentException("A URI must be provided");
        }

        $response = new HtmlResponse($this->getApplication());

        $response
            ->setStatusCode($args->get("statusCode", 302))
            ->getHeaders()
                ->set("Location", $uri);

        return $response
            ->render(
                '
                    <div class="jumbotron">
                        <h1>Redirecting&hellip;</h1>
                        <p>
                            You are being redirected to
                            <a href="' . $response->escAttr($uri) . '">' . $response->esc($uri) . '</a>
                        </p>
                    </div>
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
