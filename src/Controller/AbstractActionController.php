<?php
namespace Panadas\Controller;

abstract class AbstractActionController extends \Panadas\Controller\AbstractController
{

    /**
     * @param  \Panadas\Http\Request $request
     * @return \Panadas\Http\Response
     */
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

    /**
     * @param  \Panadas\Http\Request $request
     * @return \Panadas\Controller\AbstractActionController
     */
    protected function before(\Panadas\Http\Request $request)
    {
        return $this;
    }

    /**
     * @param  \Panadas\Http\Request $request
     * @return \Panadas\Controller\AbstractActionController
     */
    protected function after(\Panadas\Http\Request $request)
    {
        return $this;
    }

    /**
     * @param  \Panadas\Http\Request $request
     * @return \Panadas\Http\Response
     */
    protected function head(\Panadas\Http\Request $request)
    {
        return $this->get($request);
    }

    /**
     * @param  \Panadas\Http\Request $request
     * @return \Panadas\Http\Response
     */
    protected function get(\Panadas\Http\Request $request)
    {
        return $request->errorBadRequest();
    }

    /**
     * @param  \Panadas\Http\Request $request
     * @return \Panadas\Http\Response
     */
    protected function post(\Panadas\Http\Request $request)
    {
        return $request->errorBadRequest();
    }

    /**
     * @param  \Panadas\Http\Request $request
     * @return \Panadas\Http\Response
     */
    protected function put(\Panadas\Http\Request $request)
    {
        return $request->errorBadRequest();
    }

    /**
     * @param  \Panadas\Http\Request $request
     * @return \Panadas\Http\Response
     */
    protected function delete(\Panadas\Http\Request $request)
    {
        return $request->errorBadRequest();
    }
}
