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
     * @see \Panadas\Http\Response::getBody()
     * @param  boolean $as_array
     * @return mixed   $body
     */
    public function getBody($as_array = true)
    {
        return json_decode(parent::getBody(), $as_array);
    }

    /**
     * @see \Panadas\Http\Response::setBody()
     * @param  mixed $body
     * @return \Panadas\Http\Response\Json
     */
    public function setBody($body)
    {
        parent::setBody(json_encode($body));

        return $this;
    }

}
