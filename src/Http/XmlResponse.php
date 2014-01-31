<?php
namespace Panadas\Http;

class XmlResponse extends \Panadas\Http\Response
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

        $this->setContentType("application/xml");
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
        $domDocument->loadXML($content, $options);

        if (false === $domDocument) {
            throw new \Exception("Failed to load XML");
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
