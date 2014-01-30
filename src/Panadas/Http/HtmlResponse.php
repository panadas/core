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
     * @param  string $content
     * @return \Panadas\Http\HtmlResponse
     */
    public function prependContent($content)
    {
        $existingContent = $this->getContent();

        if (false === mb_strpos($existingContent, "<body>", null, $this->getCharset())) {
            return parent::prependContent($content);
        }

        return $this->setContent(str_replace("<body>", "<body>{$content}", $existingContent));
    }

    /**
     * @param  string $content
     * @return \Panadas\Http\HtmlResponse
     */
    public function appendContent($content)
    {
        $existingContent = $this->getContent();

        if (false === mb_strpos($existingContent, "</body>", null, $this->getCharset())) {
            return parent::appendContent($content);
        }

        return $this->setContent(str_replace("</body>", "{$content}</body>", $existingContent));
    }

    /**
     * @param  string $string
     * @return string
     */
    public function esc($string)
    {
        return htmlspecialchars($string, ENT_COMPAT, $this->getCharset());
    }

    /**
     * @param  string $string
     * @return string
     */
    public function escAttr($string)
    {
        return htmlspecialchars($string, ENT_QUOTES, $this->getCharset());
    }
}
