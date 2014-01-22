<?php
namespace Panadas;

abstract class ResponseAbstract extends \Panadas\AppHostAbstract
{

    private $encoding;
    private $content_type;
    private $status_code;
    private $headers = [];
    private $view_params = [];

    protected abstract function render();

    public function __construct(\Panadas\App $app, $encoding = "UTF-8")
    {
        parent::__construct($app);

        $this
            ->setEncoding($encoding)
            ->setContentType("text/plain")
            ->setStatusCode(200);
    }

    public function __toArray()
    {
        return (
            parent::__toArray()
            + [
    	        "encoding" => $this->getEncoding(),
    	        "content-type" => $this->getContentType(),
    	        "status" => [
    	            "code" => $this->getStatusCode(),
    	            "message" => $this->getStatusMessage()
                ],
    	        "headers" => $this->getAllHeaders()
            ]
        );
    }

    public function getEncoding()
    {
        return $this->encoding;
    }

    public function setEncoding($encoding)
    {
        if ( ! mb_internal_encoding($encoding)) {
            throw new \InvalidArgumentException("Encoding not supported: {$encoding}");
        }

        $this->encoding = $encoding;

        return $this;
    }

    public function getContentType()
    {
        return $this->content_type;
    }

    public function setContentType($content_type)
    {
        $this->content_type = $content_type;

        return $this;
    }

    public function getStatusCode()
    {
        return $this->status_code;
    }

    public function setStatusCode($status_code)
    {
        if ( ! \Panadas\Util\Http::hasStatusCode($status_code)) {
            throw new \InvalidArgumentException("Invalid status code: {$status_code}");
        }

        $this->status_code = $status_code;

        return $this;
    }

    public function getHeader($name, $default = null)
    {
        return $this->hasHeader($name) ? $this->headers[$name] : $default;
    }

    public function getAllHeaders()
    {
        return $this->headers;
    }

    public function getHeaderNames()
    {
        return array_keys($this->getAllHeaders());
    }

    public function hasHeader($name)
    {
        return array_key_exists($name, $this->getAllHeaders());
    }

    public function hasAnyHeaders()
    {
        return (count($this->getAllHeaders()) > 0);
    }

    public function removeHeader($name)
    {
        if ($this->hasHeader($name)) {
            unset($this->headers[$name]);
        }

        return $this;
    }

    public function removeManyHeaders(array $names)
    {
        foreach ($names as $name) {
            $this->removeHeader($name);
        }

        return $this;
    }

    public function removeAllHeaders()
    {
        return $this->removeManyHeaders($this->getHeaderNames());
    }

    public function replaceHeaders(array $headers)
    {
        return $this->removeAllHeaders()->setManyHeaders($headers);
    }

    public function setHeader($name, $value)
    {
        $this->headers[$name] = $value;

        return $this;
    }

    public function setManyHeaders(array $data)
    {
        foreach ($data as $name => $value) {
            $this->setHeader($name, $value);
        }

        return $this;
    }

    public function get($name, $default = null)
    {
        return $this->has($name) ? $this->view_params[$name] : $default;
    }

    public function getAll()
    {
        return $this->view_params;
    }

    public function getNames()
    {
        return array_keys($this->getAll());
    }

    public function has($name)
    {
        return array_key_exists($name, $this->getAll());
    }

    public function hasAny()
    {
        return (count($this->getAll()) > 0);
    }

    public function remove($name)
    {
        if ($this->has($name)) {
            unset($this->view_params[$name]);
        }

        return $this;
    }

    public function removeMany(array $names)
    {
        foreach ($names as $name) {
            $this->remove($name);
        }

        return $this;
    }

    public function removeAll()
    {
        return $this->removeMany($this->getNames());
    }

    public function replace(array $view_params)
    {
        return $this->removeAll()->setMany($view_params);
    }

    public function set($name, $value)
    {
        $this->view_params[$name] = $value;

        return $this;
    }

    public function setMany(array $view_params)
    {
        foreach ($view_params as $name => $value) {
            $this->set($name, $value);
        }

        return $this;
    }

    public function getStatusMessage()
    {
        return \Panadas\Util\Http::getStatusMessageFromCode($this->getStatusCode());
    }

    public function send(\Panadas\Request $request)
    {
        if (!headers_sent()) {

            $this->sendHeaders();

        } else {

            $logger = $this->getApp()->getServiceContainer()->get("logger", false);
            if (!is_null($logger)) {
                $logger->warn("Cannot send headers as some headers have already been sent");
            }

        }

        if (($this->getStatusCode() !== 204) && !$request->isHead()) {

            $app = $this->getApp();

            $this

                ->setMany(
                    [
                        "_app" => $app->__toArray(),
                        "_request" => $request->__toArray(),
                        "_response" => $this->__toArray()
                    ]
                )

                ->sendBody();

        }

        return $this;
    }

    protected function sendHeaders()
    {
        header("HTTP/1.1 {$this->getStatusCode()} {$this->getStatusMessage()}", true);
        header("Content-Type: {$this->getContentType()}; charset={$this->getEncoding()}", true);

        foreach ($this->getAllHeaders() as $name => $value) {
            header("{$name}: {$value}", true);
        }

        return $this;
    }

    protected function sendBody()
    {
        echo $this->render();

        return $this;
    }

}
