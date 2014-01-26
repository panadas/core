<?php
namespace Controller\Action;

class Homepage extends \Panadas\Controller\AbstractAction
{

    protected function get(\Panadas\Http\Request $request)
    {
        return (new \Panadas\Http\Response($this->getKernel()))
            ->setBody("Homepage");
    }

}
