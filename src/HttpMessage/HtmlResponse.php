<?php
namespace Panadas\Framework\HttpMessage;

use Panadas\Framework\Application;
use Panadas\Framework\ApplicationAwareInterface;
use Panadas\Framework\ApplicationAwareTrait;
use Panadas\HttpMessageModule\DataStructure\Headers;
use Panadas\HttpMessageModule\DataStructure\ResponseCookies;
use Panadas\HttpMessageModule\HtmlResponse as BaseHttpResponse;

class HtmlResponse extends BaseHttpResponse implements ApplicationAwareInterface
{

    use ApplicationAwareTrait;

    private $title;

    public function __construct(
        Application $application,
        $charset = null,
        Headers $headers = null,
        ResponseCookies $cookies = null
    ) {
        parent::__construct($charset, $headers, $cookies);

        $this
            ->setApplication($application)
            ->setTitle($application->getName());
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    public function render($content)
    {
        $content = "<!DOCTYPE html>\n" . <<<CONTENT
            <html lang="en">
                <head>
                    <meta charset="{$this->escAttr($this->getCharset())}">
                    <meta name="viewport" content="width=device-width, initial-scale=1.0">
                    <link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.0.3/css/bootstrap.min.css">
                    <title>{$this->esc($this->getTitle())}</title>
                    <style>
                        body {
                            padding-top: 60px;
                        }
                    </style>
                </head>
                <body>
                    <div class="container">
                        {$content}
                    </div>
                </body>
            </html>
CONTENT;

        return $this->setContent($content);
    }
}
