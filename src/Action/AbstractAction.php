<?php
namespace Panadas\Framework\Action;

use Panadas\DataStructure\Hash;
use Panadas\Framework\AbstractApplicationAware;
use Panadas\Framework\Application;
use Panadas\HttpMessage\Request;
use Panadas\HttpMessage\Response;

abstract class AbstractAction extends AbstractApplicationAware
{

    private $args;

    public function __construct(Application $application, array $args = [])
    {
        parent::__construct($application);

        $this->setArgs(new Hash($args));
    }

    public function getArgs()
    {
        return $this->args;
    }

    protected function setArgs(Hash $args)
    {
        $this->args = $args;

        return $this;
    }

    public function handle(Request $request)
    {
        $method = $request->getMethod();

        $supportedMethods = [
            Request::METHOD_HEAD,
            Request::METHOD_GET,
            Request::METHOD_POST,
            Request::METHOD_PUT,
            Request::METHOD_DELETE
        ];

        if (!in_array($method, $supportedMethods)) {

            $logger = $this->getApplication()->getServices()->get("logger");
            if ($logger) {
                $logger->warn("Invalid request method: {$method}");
            }

            $method = Request::METHOD_GET;

        }

        $handler = mb_strtolower($method);

        $response = $this->before($request);
        if ($response instanceof Response) {
            return $response;
        }

        $response = $this->$handler($request);

        $this->after($request, $response);

        return $response;
    }

    protected function before(Request $request)
    {
    }

    protected function after(Request $request, Response $response)
    {
    }

    protected function head(Request $request)
    {
        return $this->get($request);
    }

    protected function get(Request $request)
    {
        return $this->getApplication()->httpError400();
    }

    protected function post(Request $request)
    {
        return $this->getApplication()->httpError400();
    }

    protected function put(Request $request)
    {
        return $this->getApplication()->httpError400();
    }

    protected function delete(Request $request)
    {
        return $this->getApplication()->httpError400();
    }
}
