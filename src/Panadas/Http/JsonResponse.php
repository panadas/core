<?php
namespace Panadas\Http;

class JsonResponse extends \Panadas\Http\Response
{

    /**
     * @param \Panadas\Kernel\HttpKernel $kernel
     * @param string                     $charset
     * @param array                      $headers
     * @param mixed                      $content
     */
    public function __construct(
        \Panadas\Kernel\HttpKernel $kernel,
        $charset = null,
        array $headers = [],
        $content = null
    ) {
        parent::__construct($kernel, $charset, $headers, $content);

        $this->setContentType("application/json");
    }

    /**
     * @param  boolean $array
     * @return mixed
     */
    public function getContent($decode = false, $array = true)
    {
        $content = parent::getContent();

        if ($decode) {
            return json_decode($content, $array);
        }

        return $content;
    }

    /**
     * @param  mixed $content
     * @return \Panadas\Http\JsonResponse
     */
    public function setContent($content)
    {
        parent::setContent(json_encode($content));

        return $this;
    }
}
