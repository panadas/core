<?php
namespace Panadas\Framework\Action;

use Panadas\HttpMessageModule\Request;
use Panadas\Framework\HttpMessage\HtmlResponse;

class Homepage extends AbstractAction
{

    protected function get(Request $request)
    {
        return (new HtmlResponse($this->getApplication()))->render(
            '
                <div class="jumbotron">
                    <h1>It works!</h1>
                    <h2>Congratulations, you have successfully created a new application!</h2>
                    <p>Read the <a href="#">documentation</a> to find out what to do next.</p>
                </div>
            '
        );
    }
}
