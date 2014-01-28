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
     * @param  boolean $within_body
     * @return \Panadas\Http\HtmlResponse
     */
    public function prependContent($content, $within_body = true)
    {
        if ($within_body) {

            $existing_content = $this->getContent();

            if (false !== mb_strpos($existing_content, "<body>", null, $this->getCharset())) {
                return $this->setContent(str_replace("<body>", "<body>{$content}", $existing_content));
            }

        }

        return parent::prependContent($content);
    }

    /**
     * @param  string  $content
     * @param  boolean $within_body
     * @return \Panadas\Http\HtmlResponse
     */
    public function appendContent($content, $within_body = true)
    {
        if ($within_body) {

            $existing_content = $this->getContent();

            if (false !== mb_strpos($existing_content, "</body>", null, $this->getCharset())) {
                return $this->setContent(str_replace("</body>", "{$content}</body>", $existing_content));
            }

        }

        return parent::appendContent($content);
    }

}
