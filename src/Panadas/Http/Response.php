<?php
namespace Panadas\Http;

class Response extends \Panadas\Http\AbstractKernelAware
{

    private $encoding;
    private $content_type;
    private $status_code;
    private $headers = [];
    private $body;

    /**
     * @param \Panadas\Http\Kernel $kernel
     * @param string               $encoding
     */
    public function __construct(\Panadas\Http\Kernel $kernel, $encoding = "UTF-8")
    {
        parent::__construct($kernel);

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
     * @return \Panadas\Http\Response
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
     * @return \Panadas\Http\Response
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
     * @return \Panadas\Http\Response
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
     * @return \Panadas\Http\Response
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
     * @return \Panadas\Http\Response
     */
    public function removeManyHeaders(array $names)
    {
        foreach ($names as $name) {
            $this->removeHeader($name);
        }

        return $this;
    }

    /**
     * @return \Panadas\Http\Response
     */
    public function removeAllHeaders()
    {
        return $this->removeManyHeaders($this->getHeaderNames());
    }

    /**
     * @param  array $headers
     * @return \Panadas\Http\Response
     */
    public function replaceHeaders(array $headers)
    {
        return $this->removeAllHeaders()->setManyHeaders($headers);
    }

    /**
     * @param  string $name
     * @param  string $value
     * @return \Panadas\Http\Response
     */
    public function setHeader($name, $value)
    {
        $this->headers[$name] = $value;

        return $this;
    }

    /**
     * @param  array $data
     * @return \Panadas\Http\Response
     */
    public function setManyHeaders(array $data)
    {
        foreach ($data as $name => $value) {
            $this->setHeader($name, $value);
        }

        return $this;
    }

    /**
     * @return string
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * @return boolean
     */
    public function hasBody()
    {
        return (null !== $this->getBody());
    }

    /**
     * @param  string $body
     * @return \Panadas\Http\Response
     */
    public function setBody($body)
    {
        $this->body = $body;

        return $this;
    }

    /**
     * @return \Panadas\Http\Response
     */
    public function removeBody()
    {
        return $this->setBody(null);
    }

    /**
     * @return string
     */
    public function getStatusMessage()
    {
        return \Panadas\Util\Http::getStatusMessageFromCode($this->getStatusCode());
    }

    /**
     * @param  \Panadas\Http\Request $request
     * @return \Panadas\Http\Response
     */
    public function send(\Panadas\Http\Request $request)
    {
        if ( ! headers_sent()) {

            $this->sendHeaders();

        } else {

            $logger = $this->getKernel()->getServiceContainer()->get("logger", false);
            if (null !== $logger) {
                $logger->warn("Cannot send headers as some headers have already been sent");
            }

        }

        if (($this->getStatusCode() !== 204) && ! $request->isHead()) {
            $this->sendBody();
        }

        return $this;
    }

    /**
     * @return \Panadas\Http\Response
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
     * @return \Panadas\Http\Response
     */
    protected function sendBody()
    {
        echo $this->getBody();

        return $this;
    }

}
