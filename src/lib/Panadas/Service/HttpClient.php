<?php
namespace Panadas\Service;

class HttpClient extends \Panadas\ServiceAbstract
{

    private $request;

    public function __construct(\Panadas\App $app, \Panadas\Module\HttpClient\Request $request)
    {
        parent::__construct($app);

        $this->setRequest($request);
    }

    protected function getRequest()
    {
        return $this->request;
    }

    protected function setRequest(\Panadas\Module\HttpClient\Request $request)
    {
        $this->request = $request;

        return $this;
    }

    protected function send($method, $uri, array $params = [], array $headers = [])
    {
        return $this->getRequest()->send($method, $uri, $data, $headers);
    }

    public function get($uri, array $params = [], array $headers = [])
    {
        $request = $this->getRequest();

        return $this->send($request::METHOD_GET, $uri, $params, $headers);
    }

    public function post($uri, array $params = [], array $headers = [])
    {
        $request = $this->getRequest();

        return $this->send($request::METHOD_POST, $uri, $params, $headers);
    }

    public function put($uri, array $params = [], array $headers = [])
    {
        $request = $this->getRequest();

        return $this->send($request::METHOD_PUT, $uri, $params, $headers);
    }

    public function delete($uri, array $params = [], array $headers = [])
    {
        $request = $this->getRequest();

        return $this->send($request::METHOD_DELETE, $uri, $params, $headers);
    }

}
