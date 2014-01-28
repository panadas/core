<?php
namespace Panadas\Http;

class JsonResponse extends \Panadas\Http\Response
{

    /**
     * @param \Panadas\Http\Kernel $kernel
     */
    public function __construct(\Panadas\Http\Kernel $kernel)
    {
        parent::__construct($kernel);

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
