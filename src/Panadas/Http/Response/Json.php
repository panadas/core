<?php
namespace Panadas\Http\Response;

class Json extends \Panadas\Http\Response
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
     * @param  boolean $as_array
     * @return mixed
     */
    public function getContent($as_array = true)
    {
        return json_decode(parent::getContent(), $as_array);
    }

    /**
     * @param  mixed $content
     * @return \Panadas\Http\Response\Json
     */
    public function setContent($content)
    {
        parent::setContent(json_encode($content));

        return $this;
    }

}
