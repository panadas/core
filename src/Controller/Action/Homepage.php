<?php
namespace Controller\Action;

class Homepage extends \Panadas\Controller\AbstractAction
{

    protected function get(\Panadas\Http\Request $request)
    {
        $kernel = $this->getKernel();

        return (new \Panadas\Http\Response\Html($kernel))
            ->setBody("Welcome to " . htmlspecialchars($kernel->getName()));
    }

}
