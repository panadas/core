<?php
namespace Panadas\Http;

class HtmlResponse extends \Panadas\Http\Response
{

    /**
     * @param \Panadas\Http\Kernel $kernel
     * @param string               $charset
     */
    public function __construct(\Panadas\Http\Kernel $kernel, $charset = null)
    {
        parent::__construct($kernel, $charset);

        $this->setContentType("text/html");
    }

    /**
     * @param  string  $content
     * @param  boolean $insideBody
     * @return \Panadas\Http\HtmlResponse
     */
    public function prependContent($content, $insideBody = true)
    {
        if ($insideBody) {

            $existingContent = $this->getContent();

            if (false !== mb_strpos($existingContent, "<body>", null, $this->getCharset())) {
                return $this->setContent(str_replace("<body>", "<body>{$content}", $existingContent));
            }

        }

        return parent::prependContent($content);
    }

    /**
     * @param  string  $content
     * @param  boolean $insideBody
     * @return \Panadas\Http\HtmlResponse
     */
    public function appendContent($content, $insideBody = true)
    {
        if ($insideBody) {

            $existingContent = $this->getContent();

            if (false !== mb_strpos($existingContent, "</body>", null, $this->getCharset())) {
                return $this->setContent(str_replace("</body>", "{$content}</body>", $existingContent));
            }

        }

        return parent::appendContent($content);
    }
}
