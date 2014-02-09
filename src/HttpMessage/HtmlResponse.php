<?php
namespace Panadas\Framework\HttpMessage;

use Panadas\HttpMessage\HtmlResponse as ParentHttpResponse;
use Panadas\Framework\Application;
use Panadas\Framework\ApplicationAwareInterface;

class HtmlResponse extends ParentHttpResponse implements ApplicationAwareInterface
{

    private $application;
    private $title;

    public function __construct(Application $application, array $headers = [], $content = null, $charset = null)
    {
        parent::__construct($headers, $content, $charset);

        $this
            ->setApplication($application)
            ->setTitle($application->getName());
    }

    public function getApplication()
    {
        return $this->application;
    }

    protected function setApplication(Application $application)
    {
        $this->application = $application;

        return $this;
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
