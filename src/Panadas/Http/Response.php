<?php
namespace Panadas\Http;

class Response extends \Panadas\Http\AbstractKernelAware
{

    private $charset;
    private $content_type;
    private $status_code;
    private $headers = [];
    private $content;

    static private $status_codes = [
        100 => "Continue",
        101 => "Switching Protocols",
        200 => "OK",
        201 => "Created",
        202 => "Accepted",
        203 => "Non-Authoritative Information",
        204 => "No Content",
        205 => "Reset Content",
        206 => "Partial Content",
        300 => "Multiple Choices",
        301 => "Moved Permanently",
        302 => "Found",
        303 => "See Other",
        304 => "Not Modified",
        305 => "Use Proxy",
        307 => "Temporary Redirect",
        400 => "Bad Request",
        401 => "Unauthorized",
        402 => "Payment Required",
        403 => "Forbidden",
        404 => "Not Found",
        405 => "Method Not Allowed",
        406 => "Not Acceptable",
        407 => "Proxy Authentication Required",
        408 => "Request Timeout",
        409 => "Conflict",
        410 => "Gone",
        411 => "Length Required",
        412 => "Precondition Failed",
        413 => "Request Entity Too Large",
        414 => "Request-URI Too Long",
        415 => "Unsupported Media Type",
        416 => "Requested Range Not Satisfiable",
        417 => "Expectation Failed",
        418 => "I\"m a Teapot",
        422 => "Unprocessable Entity",
        423 => "Locked",
        424 => "Failed Dependency",
        424 => "Method Failure",
        425 => "Unordered Collection",
        426 => "Upgrade Required",
        428 => "Precondition Required",
        429 => "Too Many Requests",
        431 => "Request Header Fields Too Large",
        449 => "Retry With",
        450 => "Blocked by Windows Parental Controls",
        451 => "Unavailable For Legal Reasons",
        500 => "Internal Server Error",
        501 => "Not Implemented",
        502 => "Bad Gateway",
        503 => "Service Unavailable",
        504 => "Gateway Timeout",
        505 => "HTTP Version Not Supported"
    ];

    /**
     * @param \Panadas\Http\Kernel $kernel
     * @param string               $charset
     */
    public function __construct(\Panadas\Http\Kernel $kernel, $charset = null)
    {
        parent::__construct($kernel);

        if (null === $charset) {
            $charset = mb_internal_encoding();
        }

        $this
            ->setCharset($charset)
            ->setContentType("text/plain")
            ->setStatusCode(200);
    }

    /**
     * @return string
     */
    public function getCharset()
    {
        return $this->charset;
    }

    /**
     * @param  string $charset
     * @return \Panadas\Http\Response
     */
    public function setCharset($charset)
    {
        $this->charset = $charset;

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
        if ( ! static::hasStatusCode($status_code)) {
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
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @return boolean
     */
    public function hasContent()
    {
        return (null !== $this->getContent());
    }

    /**
     * @param  string $content
     * @return \Panadas\Http\Response
     */
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * @return \Panadas\Http\Response
     */
    public function removeContent()
    {
        return $this->setContent(null);
    }

    /**
     * @param  string $content
     * @return \Panadas\Http\Response
     */
    public function prependContent($content)
    {
        return $this->setContent($content . $this->getContent());
    }

    /**
     * @param  string $content
     * @return \Panadas\Http\Response
     */
    public function appendContent($content)
    {
        return $this->setContent($this->getContent() . $content);
    }

    /**
     * @return boolean
     */
    public function isSuccess()
    {
        $status_code = $this->getStatusCode();

        return (($status_code >= 200) && ($status_code < 300));
    }

    /**
     * @return boolean
     */
    public function isRedirect()
    {
        $status_code = $this->getStatusCode();

        return (($status_code >= 300) && ($status_code < 400));
    }

    /**
     * @return boolean
     */
    public function isClientError()
    {
        $status_code = $this->getStatusCode();

        return (($status_code >= 400) && ($status_code < 500));
    }

    /**
     * @return boolean
     */
    public function isServerError()
    {
        $status_code = $this->getStatusCode();

        return (($status_code >= 500) && ($status_code < 600));
    }

    /**
     * @return boolean
     */
    public function isError()
    {
        return ($this->isClientError() || $this->isServerError());
    }

    /**
     * @return \Panadas\Http\Response
     */
    public function send()
    {
        return $this
            ->sendHeaders()
            ->sendContent();
    }

    /**
     * @return \Panadas\Http\Response
     */
    protected function sendHeaders()
    {
        if (headers_sent()) {

            $logger = $this->getKernel()->getServiceContainer()->get("logger", false);

            if (null !== $logger) {
                $logger->warn("Cannot send headers as some headers have already been sent");
            }

            return $this;
        }

        $status_code = $this->getStatusCode();
        $status_message = static::getStatusMessage($status_code);

        header("HTTP/1.1 {$status_code} {$status_message}", true);

        $headers = $this->getAllHeaders();
        $headers["Content-Type"] = "{$this->getContentType()}; charset={$this->getCharset()}";

        foreach ($headers as $name => $value) {
            header("{$name}: {$value}", true);
        }

        return $this;
    }

    /**
     * @return \Panadas\Http\Response
     */
    protected function sendContent()
    {
        if ($this->hasContent()) {
            echo $this->getContent();
        }

        return $this;
    }

    /**
     * @return array
     */
    public static function getStatusCodes()
    {
        return array_keys(static::$status_codes);
    }

    /**
     * @param  integer $status_code
     * @return boolean
     */
    public static function hasStatusCode($status_code)
    {
        return array_key_exists($status_code, static::getStatusCodes());
    }

    /**
     * @param  integer $status_code
     * @return string
     */
    public static function getStatusMessage($status_code)
    {
        return static::hasStatusCode($code) ? static::$status_codes[$code] : null;
    }

}
