<?php
namespace Panadas\Http\Response;

class Html extends \Panadas\Http\Response
{

    /**
     * @param \Panadas\Http\Kernel $kernel
     */
    public function __construct(\Panadas\Http\Kernel $kernel)
    {
        parent::__construct($kernel);

        $this->setContentType("text/html");
    }

}
