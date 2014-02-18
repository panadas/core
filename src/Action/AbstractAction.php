<?php
namespace Panadas\Framework\Action;

use Panadas\Framework\AbstractApplicationAware;
use Panadas\Framework\Application;
use Panadas\Framework\DataStructure\ActionArgs;
use Panadas\HttpMessageModule\Request;
use Panadas\HttpMessageModule\Response;

abstract class AbstractAction extends AbstractApplicationAware
{

    private $args;

    public function __construct(Application $application, ActionArgs $args = null)
    {
        parent::__construct($application);

        if (null === $args) {
            $args = new ActionArgs();
        }

        $this->setArgs($args);
    }

    public function getArgs()
    {
        return $this->args;
    }

    protected function setArgs(ActionArgs $args)
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
            if (null !== $logger) {
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
