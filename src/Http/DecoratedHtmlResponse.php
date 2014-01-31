<?php
namespace Panadas\Http;

class DecoratedHtmlResponse extends \Panadas\Http\HtmlResponse
{

    /**
     * @param  string $content
     * @return \Panadas\Http\DecoratedHtmlResponse
     */
    public function decorate($content)
    {
        $name = $this->esc($this->getKernel()->getName());

        $content = "<!DOCTYPE html>\n" . <<<CONTENT
            <html lang="en">
                <head>
                    <meta charset="{$this->escAttr($this->getCharset())}">
                    <meta name="viewport" content="width=device-width, initial-scale=1.0">
                    <link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.0.3/css/bootstrap.min.css">
                    <title>{$name}</title>
                    <style>
                        body {padding-top: 70px}
                    </style>
                </head>
                <body>
                    <nav class="navbar navbar-default navbar-fixed-top" role="navigation">
                        <div class="container">
                            <div class="navbar-header">
                                <a class="navbar-brand" href="javascript:">{$name}</a>
                            </div>
                        </div>
                    </nav>
                    <div class="container">{$content}</div>
                </body>
            </html>
CONTENT;

        return $this->setContent($content);
    }
}
