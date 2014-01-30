<?php
namespace Panadas\Controller;

abstract class AbstractActionController extends \Panadas\Controller\AbstractController
{

    public function handle(\Panadas\Http\Request $request)
    {
        $requestMethod = $request->getMethod();

        if (!
            in_array(
                $requestMethod,
                [
                    \Panadas\Http\Request::METHOD_HEAD,
                    \Panadas\Http\Request::METHOD_GET,
                    \Panadas\Http\Request::METHOD_POST,
                    \Panadas\Http\Request::METHOD_PUT,
                    \Panadas\Http\Request::METHOD_DELETE
                ]
            )
        ) {
            $logger = $this->getKernel()->getServiceContainer()->get("logger", false);

            if (null !== $logger) {
                $logger->warn("Invalid request method: {$requestMethod}");
            }

            $requestMethod = \Panadas\Http\Request::METHOD_GET;
        }

        $methodName = mb_strtolower($requestMethod);

        foreach (["before", $methodName, "after"] as $method) {

            $result = $this->$method($request);

            if ($result instanceof \Panadas\Http\Response) {
                return $result;
            }

        }

        return null;
    }

    protected function before(\Panadas\Http\Request $request)
    {
        return $this;
    }

    protected function after(\Panadas\Http\Request $request)
    {
        return $this;
    }

    protected function head(\Panadas\Http\Request $request)
    {
        return $this->get($request)->removeContent();
    }

    protected function get(\Panadas\Http\Request $request)
    {
        return $request->errorBadRequest();
    }

    protected function post(\Panadas\Http\Request $request)
    {
        return $request->errorBadRequest();
    }

    protected function put(\Panadas\Http\Request $request)
    {
        return $request->errorBadRequest();
    }

    protected function delete(\Panadas\Http\Request $request)
    {
        return $request->errorBadRequest();
    }
}
