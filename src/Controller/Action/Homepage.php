<?php
namespace Controller\Action;

class Homepage extends \Panadas\Controller\AbstractActionController
{

    protected function get(\Panadas\Http\Request $request)
    {
        $kernel = $this->getKernel();

        if ($request->isAjax()) {

            $response = new \Panadas\Http\JsonResponse($kernel);

        } elseif ($kernel->getServiceContainer()->has("twig")) {

            $response = new \Panadas\TwigModule\Http\TwigResponse($kernel, "Homepage.twig");

        } else {

            $response = (new \Panadas\Http\HtmlResponse($kernel))
                ->setContent("Welcome to " . htmlspecialchars($kernel->getName()));

        }

        return $response;
    }

}
