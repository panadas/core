<?php
namespace Panadas\Response;

class Json extends \Panadas\ResponseAbstract
{

    public function __construct(\Panadas\App $app, $encoding = "UTF-8")
    {
        parent::__construct($app, $encoding);

        $this->setContentType("application/json");
    }

    protected function render()
    {
        $options = 0;

        if ($this->getApp()->isDebugMode()) {
            $options += JSON_PRETTY_PRINT;
        }

        return json_encode($this->getAll(), $options);
    }

}
