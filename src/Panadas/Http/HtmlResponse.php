<?php
namespace Panadas\Http;

class HtmlResponse extends \Panadas\Http\Response
{

    /**
     * @param \Panadas\Kernel\Kernel $kernel
     * @param string                 $charset
     * @param array                  $headers
     * @param string                 $content
     */
    public function __construct(\Panadas\Kernel\Kernel $kernel, $charset = null, array $headers = [], $content = null)
    {
        parent::__construct($kernel, $charset, $headers, $content);

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
