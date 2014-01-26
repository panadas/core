<?php
namespace Controller\Action;

class Homepage extends \Panadas\Controller\AbstractAction
{

    protected function get(\Panadas\Http\Request $request)
    {
        $kernel = $this->getKernel();

        if ($request->isAjax()) {

            $response = new \Panadas\Http\Response\Json($kernel);

        } elseif ($kernel->getServiceContainer()->has("twig")) {

            $response = new \Panadas\TwigModule\Response($kernel, "Homepage.twig.html");

        } else {

            $response = (new \Panadas\Http\Response\Html($kernel))
                ->setContent("Welcome to " . htmlspecialchars($kernel->getName()));

        }

        return $response;
    }

}
