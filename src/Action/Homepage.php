<?php
namespace Panadas\Framework\Action;

use Panadas\HttpMessage\Request;
use Panadas\Framework\HttpMessage\HtmlResponse;

class Homepage extends AbstractAction
{

    protected function get(Request $request)
    {
        return (new HtmlResponse($this->getApplication(), apache_response_headers()))
            ->render("<div class=\"jumbotron\">" . __METHOD__ . "</div>");
    }
}
