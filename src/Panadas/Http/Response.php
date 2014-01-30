<?php
namespace Panadas\Http;

class Response extends \Panadas\Kernel\AbstractKernelAware
{

    private $charset;
    private $contentType;
    private $statusCode;
    private $headers;
    private $content;

    static protected $statusCodes = [
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
     * @param \Panadas\Kernel\Kernel $kernel
     * @param string                 $charset
     * @param array                  $headers
     * @param string                 $content
     */
    public function __construct(\Panadas\Kernel\Kernel $kernel, $charset = null, array $headers = [], $content = null)
    {
        parent::__construct($kernel);

        if (null === $charset) {
            $charset = mb_internal_encoding();
        }

        $this
            ->setCharset($charset)
            ->setContentType("text/plain")
            ->setHeaders(new \Panadas\DataStructure\HashDataStructure($headers))
            ->setContent($content)
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
        return $this->contentType;
    }

    /**
     * @param  string $contentType
     * @return \Panadas\Http\Response
     */
    public function setContentType($contentType)
    {
        $this->contentType = $contentType;

        return $this;
    }

    /**
     * @return integer
     */
    public function getStatusCode()
    {
        return $this->statusCode;
    }

    /**
     * @param  integer $statusCode
     * @throws \InvalidArgumentException
     * @return \Panadas\Http\Response
     */
    public function setStatusCode($statusCode)
    {
        if (!static::hasStatusCode($statusCode)) {
            throw new \InvalidArgumentException("Invalid status code: {$statusCode}");
        }

        $this->statusCode = $statusCode;

        return $this;
    }

    /**
     * @return \Panadas\DataStructure\HashDataStructure
     */
    protected function getHeaders()
    {
        return $this->headers;
    }

    /**
     * @param  \Panadas\DataStructure\HashDataStructure $headers
     * @return \Panadas\Http\Response
     */
    protected function setHeaders(\Panadas\DataStructure\HashDataStructure $headers)
    {
        $this->headers = $headers;

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
     * @param  string $name
     * @param  mixed  $default
     * @return mixed
     */
    public function getHeader($name, $default = null)
    {
        return $this->getHeaders()->get($name, $default);
    }

    /**
     * @return array
     */
    public function getAllHeaders()
    {
        return $this->getHeaders()->getAll();
    }

    /**
     * @return array
     */
    public function getHeaderNames()
    {
        return $this->getHeaders()->getNames();
    }

    /**
     * @param  string $name
     * @return boolean
     */
    public function hasHeader($name)
    {
        return $this->getHeaders()->has($name);
    }

    /**
     * @return boolean
     */
    public function hasAnyHeaders()
    {
        return $this->getHeaders()->hasAny();
    }

    /**
     * @param  string $name
     * @return \Panadas\Http\Response
     */
    public function removeHeader($name)
    {
        $this->getHeaders()->remove($name);

        return $this;
    }

    /**
     * @return \Panadas\Http\Response
     */
    public function removeAllHeaders()
    {
        $this->getHeaders()->removeAll();

        return $this;
    }

    /**
     * @param  string $name
     * @param  mixed  $value
     * @return \Panadas\Http\Response
     */
    public function setHeader($name, $value)
    {
        $this->getHeaders()->set($name, $value);

        return $this;
    }

    /**
     * @param  array $headers
     * @return \Panadas\Http\Response
     */
    public function replaceHeaders(array $headers)
    {
        $this->getHeaders()->replace($headers);

        return $this;
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
        $statusCode = $this->getStatusCode();

        return (($statusCode >= 200) && ($statusCode < 300));
    }

    /**
     * @return boolean
     */
    public function isRedirect()
    {
        $statusCode = $this->getStatusCode();

        return (($statusCode >= 300) && ($statusCode < 400));
    }

    /**
     * @return boolean
     */
    public function isClientError()
    {
        $statusCode = $this->getStatusCode();

        return (($statusCode >= 400) && ($statusCode < 500));
    }

    /**
     * @return boolean
     */
    public function isServerError()
    {
        $statusCode = $this->getStatusCode();

        return (($statusCode >= 500) && ($statusCode < 600));
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

        $statusCode = $this->getStatusCode();
        $statusMessage = static::getStatusMessage($statusCode);

        header("HTTP/1.1 {$statusCode} {$statusMessage}", true);

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
        return static::$statusCodes;
    }

    /**
     * @param  integer $statusCode
     * @return boolean
     */
    public static function hasStatusCode($statusCode)
    {
        return array_key_exists($statusCode, static::getStatusCodes());
    }

    /**
     * @param  integer $statusCode
     * @return string
     */
    public static function getStatusMessage($statusCode)
    {
        return static::hasStatusCode($statusCode) ? static::$statusCodes[$statusCode] : null;
    }

    /**
     * @param  \Panadas\Http\Request $request
     * @return \Panadas\Http\Response
     */
    public static function create(\Panadas\Http\Request $request)
    {
        $kernel = $request->getKernel();

        if ($request->isAjax()) {
            return new \Panadas\Http\JsonResponse($kernel);
        }

        return new \Panadas\Http\HtmlResponse($kernel);
    }
}
