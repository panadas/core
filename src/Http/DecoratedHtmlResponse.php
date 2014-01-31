<?php
namespace Panadas\Http;

class DecoratedHtmlResponse extends \Panadas\Http\HtmlResponse implements \Panadas\Http\DecoratedResponseInterface
{

    private $title;

    /**
     * @param \Panadas\Kernel\Kernel $kernel
     * @param string                 $charset
     * @param array                  $headers
     * @param string                 $content
     */
    public function __construct(\Panadas\Kernel\Kernel $kernel, $charset = null, array $headers = [])
    {
        parent::__construct($kernel, $charset, $headers);

        $this->setTitle($kernel->getName());
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @return boolean
     */
    public function hasTitle()
    {
        return (null !== $this->getTitle());
    }

    /**
     * @param  string $title
     * @return \Panadas\Http\DecoratedHtmlResponse
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @return \Panadas\Http\DecoratedHtmlResponse
     */
    public function removeTitle()
    {
        return $this->setTitle(null);
    }

    /**
     * @param  string $content
     * @return \Panadas\Http\DecoratedHtmlResponse
     */
    public function decorate($content)
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
