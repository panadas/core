<?php
namespace Panadas\Controller;

class DefaultActionController extends \Panadas\Controller\AbstractActionController
{

    /**
     * @param  \Panadas\Http\Request $request
     * @return \Panadas\Http\Response
     */
    protected function get(\Panadas\Http\Request $request)
    {
        $content = <<<CONTENT
            <div class="jumbotron">
                <h1>It works!</h1>
                <h2>Congratulations, you have successfully created a new application!</h2>
                <p>Read the <a href="#">documentation</a> to find out what to do next.</p>
            </div>
CONTENT;

        return \Panadas\Http\DecoratedHtmlResponse::create($request->getKernel())
            ->setContent($content);
    }
}
