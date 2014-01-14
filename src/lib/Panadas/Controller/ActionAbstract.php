<?php
namespace Panadas\Controller;

abstract class ActionAbstract extends \Panadas\ControllerAbstract
{

    public function run(\Panadas\Request $request, \Panadas\ResponseAbstract $response)
    {
        $request_method = $request->getMethod();

        switch ($request_method) {
            case \Panadas\Request::METHOD_HEAD:
            case \Panadas\Request::METHOD_POST:
            case \Panadas\Request::METHOD_PUT:
            case \Panadas\Request::METHOD_DELETE:
                break;
            case \Panadas\Request::METHOD_GET:
            default:
                $request_method = \Panadas\Request::METHOD_GET;
        }

        $method_name = mb_strtolower($request_method);

        foreach (["before", $method_name, "after"] as $method) {

            $result = $this->$method($request, $response);

            if ($result instanceof \Panadas\ResponseAbstract) {
                return $result;
            }

        }

        return $response;
    }

    protected function before(\Panadas\Request $request, \Panadas\ResponseAbstract $response)
    {
        return $this;
    }

    protected function after(\Panadas\Request $request, \Panadas\ResponseAbstract $response)
    {
        return $this;
    }

    protected function invalid(\Panadas\Request $request, \Panadas\ResponseAbstract $response, $message = null)
    {
        if (is_null($message)) {
            $message = "HTTP method not supported: {$request->getMethod()}";
        }

        return $this->getApp()->error400($message);
    }

    protected function head(\Panadas\Request $request, \Panadas\ResponseAbstract $response)
    {
        return $this->get($request, $response);
    }

    protected function get(\Panadas\Request $request, \Panadas\ResponseAbstract $response)
    {
        return $this->invalid($request, $response);
    }

    protected function post(\Panadas\Request $request, \Panadas\ResponseAbstract $response)
    {
        return $this->invalid($request, $response);
    }

    protected function put(\Panadas\Request $request, \Panadas\ResponseAbstract $response)
    {
        return $this->invalid($request, $response);
    }

    protected function delete(\Panadas\Request $request, \Panadas\ResponseAbstract $response)
    {
        return $this->invalid($request, $response);
    }

    public static function getClassName($name)
    {
        return "Controller\Action\\{$name}";
    }

}
