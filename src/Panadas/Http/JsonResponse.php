<?php
namespace Panadas\Http;

class JsonResponse extends \Panadas\Http\Response
{

    /**
     * @param \Panadas\Http\Kernel $kernel
     * @param string               $charset
     * @param array                $headers
     * @param mixed                $content
     */
    public function __construct(\Panadas\Http\Kernel $kernel, $charset = null, array $headers = [], $content = null)
    {
        parent::__construct($kernel, $charset, $headers, $content);

        $this->setContentType("application/json");
    }

    /**
     * @param  boolean $array
     * @return mixed
     */
    public function getContent($array = true)
    {
        return json_decode(parent::getContent(), $array);
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
