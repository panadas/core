<?php
namespace Panadas;

abstract class AbstractResponse extends \Panadas\AppContainer
{

    private $encoding;
    private $content_type;
    private $status_code;
    private $headers = [];
    private $view_params = [];

    /**
     * @return string
     */
    protected abstract function render();

    /**
     * @param \Panadas\App $app
     * @param string       $encoding
     */
    public function __construct(\Panadas\App $app, $encoding = "UTF-8")
    {
        parent::__construct($app);

        $this
            ->setEncoding($encoding)
            ->setContentType("text/plain")
            ->setStatusCode(200);
    }

    /**
     * @see \Panadas\AbstractBase::__toArray()
     * @return array
     */
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

    /**
     * @return string
     */
    public function getEncoding()
    {
        return $this->encoding;
    }

    /**
     * @param  string $encoding
     * @throws \InvalidArgumentException
     * @return \Panadas\AbstractResponse
     */
    public function setEncoding($encoding)
    {
        if ( ! mb_internal_encoding($encoding)) {
            throw new \InvalidArgumentException("Encoding not supported: {$encoding}");
        }

        $this->encoding = $encoding;

        return $this;
    }

    /**
     * @return string
     */
    public function getContentType()
    {
        return $this->content_type;
    }

    /**
     * @param  string $content_type
     * @return \Panadas\AbstractResponse
     */
    public function setContentType($content_type)
    {
        $this->content_type = $content_type;

        return $this;
    }

    /**
     * @return integer
     */
    public function getStatusCode()
    {
        return $this->status_code;
    }

    /**
     * @param  integer $status_code
     * @throws \InvalidArgumentException
     * @return \Panadas\AbstractResponse
     */
    public function setStatusCode($status_code)
    {
        if ( ! \Panadas\Util\Http::hasStatusCode($status_code)) {
            throw new \InvalidArgumentException("Invalid status code: {$status_code}");
        }

        $this->status_code = $status_code;

        return $this;
    }

    /**
     * @param  string $name
     * @param  mixed  $default
     * @return mixed
     */
    public function getHeader($name, $default = null)
    {
        return $this->hasHeader($name) ? $this->headers[$name] : $default;
    }

    /**
     * @return array
     */
    public function getAllHeaders()
    {
        return $this->headers;
    }

    /**
     * @return array
     */
    public function getHeaderNames()
    {
        return array_keys($this->getAllHeaders());
    }

    /**
     * @param  string $name
     * @return boolean
     */
    public function hasHeader($name)
    {
        return array_key_exists($name, $this->getAllHeaders());
    }

    /**
     * @return boolean
     */
    public function hasAnyHeaders()
    {
        return (count($this->getAllHeaders()) > 0);
    }

    /**
     * @param  string $name
     * @return \Panadas\AbstractResponse
     */
    public function removeHeader($name)
    {
        if ($this->hasHeader($name)) {
            unset($this->headers[$name]);
        }

        return $this;
    }

    /**
     * @param  array $names
     * @return \Panadas\AbstractResponse
     */
    public function removeManyHeaders(array $names)
    {
        foreach ($names as $name) {
            $this->removeHeader($name);
        }

        return $this;
    }

    /**
     * @return \Panadas\AbstractResponse
     */
    public function removeAllHeaders()
    {
        return $this->removeManyHeaders($this->getHeaderNames());
    }

    /**
     * @param  array $headers
     * @return \Panadas\AbstractResponse
     */
    public function replaceHeaders(array $headers)
    {
        return $this->removeAllHeaders()->setManyHeaders($headers);
    }

    /**
     * @param  string $name
     * @param  string $value
     * @return \Panadas\AbstractResponse
     */
    public function setHeader($name, $value)
    {
        $this->headers[$name] = $value;

        return $this;
    }

    /**
     * @param  array $data
     * @return \Panadas\AbstractResponse
     */
    public function setManyHeaders(array $data)
    {
        foreach ($data as $name => $value) {
            $this->setHeader($name, $value);
        }

        return $this;
    }

    /**
     * @param  string $name
     * @param  mixed  $default
     * @return mixed
     */
    public function get($name, $default = null)
    {
        return $this->has($name) ? $this->view_params[$name] : $default;
    }

    /**
     * @return array
     */
    public function getAll()
    {
        return $this->view_params;
    }

    /**
     * @return array
     */
    public function getNames()
    {
        return array_keys($this->getAll());
    }

    /**
     * @param  string $name
     * @return boolean
     */
    public function has($name)
    {
        return array_key_exists($name, $this->getAll());
    }

    /**
     * @return boolean
     */
    public function hasAny()
    {
        return (count($this->getAll()) > 0);
    }

    /**
     * @param  string $name
     * @return \Panadas\AbstractResponse
     */
    public function remove($name)
    {
        if ($this->has($name)) {
            unset($this->view_params[$name]);
        }

        return $this;
    }

    /**
     * @param  array $names
     * @return \Panadas\AbstractResponse
     */
    public function removeMany(array $names)
    {
        foreach ($names as $name) {
            $this->remove($name);
        }

        return $this;
    }

    /**
     * @return \Panadas\AbstractResponse
     */
    public function removeAll()
    {
        return $this->removeMany($this->getNames());
    }

    /**
     * @param  array $view_params
     * @return \Panadas\AbstractResponse
     */
    public function replace(array $view_params)
    {
        return $this->removeAll()->setMany($view_params);
    }

    /**
     * @param  string $name
     * @param  mixed  $value
     * @return \Panadas\AbstractResponse
     */
    public function set($name, $value)
    {
        $this->view_params[$name] = $value;

        return $this;
    }

    /**
     * @param  array $view_params
     * @return \Panadas\AbstractResponse
     */
    public function setMany(array $view_params)
    {
        foreach ($view_params as $name => $value) {
            $this->set($name, $value);
        }

        return $this;
    }

    /**
     * @return string
     */
    public function getStatusMessage()
    {
        return \Panadas\Util\Http::getStatusMessageFromCode($this->getStatusCode());
    }

    /**
     * @param  \Panadas\Request $request
     * @return \Panadas\AbstractResponse
     */
    public function send(\Panadas\Request $request)
    {
        if ( ! headers_sent()) {

            $this->sendHeaders();

        } else {

            $logger = $this->getApp()->getServiceContainer()->get("logger", false);
            if (null !== $logger) {
                $logger->warn("Cannot send headers as some headers have already been sent");
            }

        }

        if (($this->getStatusCode() !== 204) && ! $request->isHead()) {

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

    /**
     * @return \Panadas\AbstractResponse
     */
    protected function sendHeaders()
    {
        header("HTTP/1.1 {$this->getStatusCode()} {$this->getStatusMessage()}", true);
        header("Content-Type: {$this->getContentType()}; charset={$this->getEncoding()}", true);

        foreach ($this->getAllHeaders() as $name => $value) {
            header("{$name}: {$value}", true);
        }

        return $this;
    }

    /**
     * @return \Panadas\AbstractResponse
     */
    protected function sendBody()
    {
        echo $this->render();

        return $this;
    }

}
