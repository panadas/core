<?php
namespace Panadas\Http;

class JsonResponse extends \Panadas\Http\Response
{

    /**
     * @param \Panadas\Kernel\Kernel $kernel
     * @param string                 $charset
     * @param array                  $headers
     * @param mixed                  $content
     */
    public function __construct(\Panadas\Kernel\Kernel $kernel, $charset = null, array $headers = [], $content = null)
    {
        parent::__construct($kernel, $charset, $headers, $content);

        $this->setContentType("application/json");
    }

    /**
     * @param  string $content
     * @throws \RuntimeException
     */
    public function prependContent($content)
    {
        throw new \RuntimeException("Cannot prepend to JSON content");
    }

    /**
     * @param  string $content
     * @throws \RuntimeException
     */
    public function appendContent($content)
    {
        throw new \RuntimeException("Cannot append to JSON content");
    }

    /**
     * @param  boolean $raw
     * @return mixed
     */
    public function getContent($raw = true)
    {
        $content = parent::getContent();

        if ((null !== $content) && !$raw) {
            $content = json_decode($content);
        }

        return $content;
    }

    /**
     * @param  string  $content
     * @param  boolean $raw
     * @return \Panadas\Http\JsonResponse
     */
    public function setContent($content, $raw = true)
    {
        if ((null !== $content) && $raw) {
            $content = json_encode($content);
        }

        return parent::setContent($content);
    }
}
