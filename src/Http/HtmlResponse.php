<?php
namespace Panadas\Http;

class HtmlResponse extends \Panadas\Http\XmlResponse
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
     * @param  integer $options
     * @return \DOMDocument
     */
    public function getDomDocument($options = null)
    {
        $content = $this->getContent();

        if (null === $content) {
            return null;
        }

        $domDocument = new \DOMDocument(null, $this->getCharset());
        $domDocument->loadHTML($content, $options);

        if (false === $domDocument) {
            throw new \Exception("Failed to load HTML");
        }

        return $domDocument;
    }

    /**
     * @param  \DOMDocument $domDocument
     * @return \Panadas\Http\HtmlResponse
     */
    public function setDomDocument(\DOMDocument $domDocument)
    {
        return $this->setContent($domDocument->saveHTML());
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
            return parent::prependContent($content);
        }

        return $this->setContent(str_replace("</body>", "{$content}</body>", $existingContent));
    }
}
