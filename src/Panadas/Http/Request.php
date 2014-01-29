<?php
namespace Panadas\Http;

class Request extends \Panadas\Http\AbstractKernelAware
{

    private $uri;
    private $queryParams;
    private $dataParams;
    private $cookies;

    const METHOD_HEAD = "HEAD";
    const METHOD_GET = "GET";
    const METHOD_POST = "POST";
    const METHOD_PUT = "PUT";
    const METHOD_DELETE = "DELETE";

    const PARAM_METHOD = "_method";

    /**
     * @param \Panadas\Http\Kernel $kernel
     * @param array                $queryParams
     * @param array                $dataParams
     * @param array                $cookies
     */
    public function __construct(\Panadas\Http\Kernel $kernel, array $queryParams = [], array $dataParams = [], array $cookies = [])
    {
        parent::__construct($kernel);

        $this
            ->setUri($this->detectUri())
            ->setQueryParams(new \Panadas\ArrayStore\HashArrayStore($queryParams))
            ->setDataParams(new \Panadas\ArrayStore\HashArrayStore($dataParams))
            ->setCookies(new \Panadas\ArrayStore\HashArrayStore($cookies));
    }

    /**
     * @param  string $absolute
     * @param  string $query
     * @return string
     */
    public function getUri($absolute = true, $query = true)
    {
        $uri = $this->uri;

        if (!$absolute) {

            $position = mb_strpos($uri, "/", (mb_strpos($uri, "://") + 3));

            if ($position !== false) {
                $uri = mb_substr($uri, $position);
            }

            $uri = preg_replace("/^.+:\/\/[^\/]+/", "", $uri);

        }

        if (!$query) {

            $position = mb_strpos($uri, "?");

            if ($position !== false) {
                $uri = mb_substr($uri, 0, $position);
            }

        }

        return $uri;
    }

    /**
     * @param  string $uri
     * @return \Panadas\Http\Request
     */
    protected function setUri($uri)
    {
        $this->uri = $uri;

        return $this;
    }

    /**
     * @return \Panadas\ArrayStore\HashArrayStore
     */
    protected function getQueryParams()
    {
        return $this->queryParams;
    }

    /**
     * @param  \Panadas\ArrayStore\HashArrayStore $queryParams
     * @return \Panadas\Http\Request
     */
    protected function setQueryParams(\Panadas\ArrayStore\HashArrayStore $queryParams)
    {
        $this->queryParams = $queryParams;

        return $this;
    }

    /**
     * @return \Panadas\ArrayStore\HashArrayStore
     */
    protected function getDataParams()
    {
        return $this->dataParams;
    }

    /**
     * @param  \Panadas\ArrayStore\HashArrayStore $dataParams
     * @return \Panadas\Http\Request
     */
    protected function setDataParams(\Panadas\ArrayStore\HashArrayStore $dataParams)
    {
        $this->dataParams = $dataParams;

        return $this;
    }

    /**
     * @return \Panadas\ArrayStore\HashArrayStore
     */
    protected function getCookies()
    {
        return $this->cookies;
    }

    /**
     * @param  \Panadas\ArrayStore\HashArrayStore $cookies
     * @return \Panadas\Http\Request
     */
    protected function setCookies(\Panadas\ArrayStore\HashArrayStore $cookies)
    {
        $this->cookies = $cookies;

        return $this;
    }

    /**
     * @param  string $name
     * @param  mixed  $default
     * @return mixed
     */
    public function getQueryParam($name, $default = null)
    {
        return $this->getQueryParams()->get($name, $default);
    }

    /**
     * @return array
     */
    public function getAllQueryParams()
    {
        return $this->getQueryParams()->getAll();
    }

    /**
     * @return array
     */
    public function getQueryParamNames()
    {
        return $this->getQueryParams()->getNames();
    }

    /**
     * @param  string $name
     * @return boolean
     */
    public function hasQueryParam($name)
    {
        return $this->getQueryParams()->has($name);
    }

    /**
     * @return boolean
     */
    public function hasAnyQueryParams()
    {
        return $this->getQueryParams()->hasAny();
    }

    /**
     * @param  string $name
     * @return \Panadas\Http\Request
     */
    public function removeQueryParam($name)
    {
        $this->getQueryParams()->remove($name);

        return $this;
    }

    /**
     * @return \Panadas\Http\Request
     */
    public function removeAllQueryParams()
    {
        $this->getQueryParams()->removeAll();

        return $this;
    }

    /**
     * @param  string $name
     * @param  mixed  $value
     * @return \Panadas\Http\Request
     */
    public function setQueryParam($name, $value)
    {
        $this->getQueryParams()->set($name, $value);

        return $this;
    }

    /**
     * @param  array $queryParams
     * @return \Panadas\Http\Request
     */
    public function replaceQueryParams(array $queryParams)
    {
        $this->getQueryParams()->replace($queryParams);

        return $this;
    }

    /**
     * @param  string $name
     * @param  mixed  $default
     * @return mixed
     */
    public function getDataParam($name, $default = null)
    {
        return $this->getDataParams()->get($name, $default);
    }

    /**
     * @return array
     */
    public function getAllDataParams()
    {
        return $this->getDataParams()->getAll();
    }

    /**
     * @return array
     */
    public function getDataParamNames()
    {
        return $this->getDataParams()->getNames();
    }

    /**
     * @param  string $name
     * @return boolean
     */
    public function hasDataParam($name)
    {
        return $this->getDataParams()->has($name);
    }

    /**
     * @return boolean
     */
    public function hasAnyDataParams()
    {
        return $this->getDataParams()->hasAny();
    }

    /**
     * @param  string $name
     * @return \Panadas\Http\Request
     */
    public function removeDataParam($name)
    {
        $this->getDataParams()->remove($name);

        return $this;
    }

    /**
     * @return \Panadas\Http\Request
     */
    public function removeAllDataParams()
    {
        $this->getDataParams()->removeAll();

        return $this;
    }

    /**
     * @param  string $name
     * @param  mixed  $value
     * @return \Panadas\Http\Request
     */
    public function setDataParam($name, $value)
    {
        $this->getDataParams()->set($name, $value);

        return $this;
    }

    /**
     * @param  array $dataParams
     * @return \Panadas\Http\Request
     */
    public function replaceDataParams(array $dataParams)
    {
        $this->getDataParams()->replace($dataParams);

        return $this;
    }

    /**
     * @param  string $name
     * @param  mixed  $default
     * @return mixed
     */
    public function getCookie($name, $default = null)
    {
        return $this->getCookies()->get($name, $default);
    }

    /**
     * @return array
     */
    public function getAllCookies()
    {
        return $this->getCookies()->getAll();
    }

    /**
     * @return array
     */
    public function getCookieNames()
    {
        return $this->getCookies()->getNames();
    }

    /**
     * @param  string $name
     * @return boolean
     */
    public function hasCookie($name)
    {
        return $this->getCookies()->has($name);
    }

    /**
     * @return boolean
     */
    public function hasAnyCookies()
    {
        return $this->getCookies()->hasAny();
    }

    /**
     * @param  string $name
     * @return \Panadas\Http\Request
     */
    protected function removeCookie($name)
    {
        $this->getCookies()->remove($name);

        return $this;
    }

    /**
     * @return \Panadas\Http\Request
     */
    protected function removeAllCookies()
    {
        $this->getCookies()->removeAll();

        return $this;
    }

    /**
     * @param  string $name
     * @param  mixed  $value
     * @return \Panadas\Http\Request
     */
    protected function setCookie($name, $value)
    {
        $this->getCookies()->set($name, $value);

        return $this;
    }

    /**
     * @param  array $cookies
     * @return \Panadas\Http\Request
     */
    protected function replaceCookies(array $cookies)
    {
        $this->getCookies()->replace($cookies);

        return $this;
    }

    /**
     * @param  string $name
     * @param  mixed  $default
     * @return mixed
     */
    public function getHeader($name, $default = null)
    {
        return $this->getKernel()->getServerVar(static::getPhpHeaderName($name), $default);
    }

    /**
     * @param  string $name
     * @return boolean
     */
    public function hasHeader($name)
    {
        return $this->getKernel()->hasServerVar(static::getPhpHeaderName($name));
    }

    /**
     * @return string
     */
    protected function detectUri()
    {
        $kernel = $this->getKernel();
        $secure = $this->isSecure();
        $protocol = $secure ? "https" : "http";

        $port = $kernel->getServerVar("SERVER_PORT");

        if (null !== $port) {
            $port = ($port != ($secure ? 443 : 80)) ? ":{$port}" : null;
        }

        $host = $kernel->getServerVar("HTTP_HOST");
        $path = $kernel->getServerVar("PATH_INFO", $kernel->getServerVar("REQUEST_URI"));
        $query = $kernel->getServerVar("QUERY_STRING");

        if (mb_strlen($query) > 0) {
            $query = "?{$query}";
        }

        return "{$protocol}://{$host}{$port}{$path}{$query}";
    }

    /**
     * @return string
     */
    public function getMethod()
    {
        return mb_strtoupper(
            $this->get(
                static::PARAM_METHOD,
                function ($name) {
                    $this->getKernel()->getServerParam("REQUEST_METHOD", static::METHOD_GET);
                }
            )
        );
    }

    /**
     * @return boolean
     */
    public function isHead()
    {
        return ($this->getMethod() === static::METHOD_HEAD);
    }

    /**
     * @return boolean
     */
    public function isGet()
    {
        return ($this->getMethod() === static::METHOD_GET);
    }

    /**
     * @return boolean
     */
    public function isPost()
    {
        return ($this->getMethod() === static::METHOD_POST);
    }

    /**
     * @return boolean
     */
    public function isPut()
    {
        return ($this->getMethod() === static::METHOD_PUT);
    }

    /**
     * @return boolean
     */
    public function isDelete()
    {
        return ($this->getMethod() === static::METHOD_DELETE);
    }

    /**
     * @return boolean
     */
    public function isSecure()
    {
        $kernel = $this->getKernel();

        $headers = [
            "HTTPS" => "ON",
            "HTTP_X_FORWARDED_PROTO" => "HTTPS"
        ];

        foreach ($headers as $name => $value) {
            if (mb_strtoupper($kernel->getServerVar($name)) === $value) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return boolean
     */
    public function isAjax()
    {
        return (mb_strtoupper($this->getKernel()->getServerVar("HTTP_X_REQUESTED_WITH")) === "XMLHTTPREQUEST");
    }

    /**
     * @return string
     */
    public function getIp()
    {
        $kernel = $this->getKernel();

        $headers = [
            "HTTP_CLIENT_IP",
            "HTTP_X_FORWARDED_FOR",
            "REMOTE_ADDR"
        ];

        foreach ($headers as $name) {

            $value = $kernel->getServerVar($name);

            if (null === $value) {
                continue;
            }

            if (mb_strpos($value, ",") !== false) {
                $value = trim(explode(",", $value)[0]);
            }

            return $value;

        }

        return null;
    }

    /**
     * @param  \Panadas\Http\Kernel $kernel
     * @return \Panadas\Http\Request
     */
    public static function create(\Panadas\Http\Kernel $kernel)
    {
        $instance = new static($kernel, $_GET, $_POST, $_COOKIE);

        if ($instance->isPut()) {

            $body = null;
            $params = [];

            $file = fopen("php://input", "r");
            while (!feof($file)) {
                $body .= fread($file, 1024);
            }
            fclose($file);

            parse_str($body, $params);

            $instance->setMany($params);

        }

        return $instance;
    }

    /**
     * Convert a raw header name to the PHP equivalent.
     *
     * @param  string $name
     * @return string
     */
    public static function getPhpHeaderName($name)
    {
        return "HTTP_" . preg_replace("/[^0-9a-z]/i", "_", mb_strtoupper($name));
    }
}
