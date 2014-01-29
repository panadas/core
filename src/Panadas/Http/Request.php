<?php
namespace Panadas\Http;

class Request extends \Panadas\Http\AbstractKernelAware
{

    private $uri;
    private $queryParamsContainer;
    private $dataParamsContainer;
    private $cookiesContainer;

    const METHOD_HEAD = "HEAD";
    const METHOD_GET = "GET";
    const METHOD_POST = "POST";
    const METHOD_PUT = "PUT";
    const METHOD_DELETE = "DELETE";

    const PARAM_METHOD = "_method";

    public function __construct(\Panadas\Http\Kernel $kernel, array $queryParams = [], array $dataParams = [], array $cookies = [])
    {
        parent::__construct($kernel);

        $this
            ->setUri($this->detectUri())
            ->setQueryParamsContainer(new \Panadas\ParamContainer($queryParams))
            ->setDataParamsContainer(new \Panadas\ParamContainer($dataParams))
            ->setCookiesContainer(new \Panadas\ParamContainer($cookies));
    }

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

    protected function setUri($uri)
    {
        $this->uri = $uri;

        return $this;
    }

    protected function getQueryParamsContainer()
    {
        return $this->queryParamsContainer;
    }

    protected function setQueryParamsContainer(\Panadas\ParamContainer $queryParamsContainer)
    {
        $this->queryParamsContainer = $queryParamsContainer;

        return $this;
    }

    protected function getDataParamsContainer()
    {
        return $this->dataParamsContainer;
    }

    protected function setDataParamsContainer(\Panadas\ParamContainer $dataParamsContainer)
    {
        $this->dataParamsContainer = $dataParamsContainer;

        return $this;
    }

    protected function getCookiesContainer()
    {
        return $this->cookiesContainer;
    }

    protected function setCookiesContainer(\Panadas\ParamContainer $cookiesContainer)
    {
        $this->cookiesContainer = $cookiesContainer;

        return $this;
    }

    public function get($name, $default = null)
    {
        return $this->getQueryParam(
            $name,
            function ($name) use ($default) {
        	   return $this->getDataParam($name, $default);
            }
        );
    }

    public function has($name)
    {
        return ($this->hasQueryParam($name) || $this->hasDataParam($name));
    }

    public function hasAny()
    {
        return ($this->hasAnyQueryParams() || $this->hasAnyDataParams());
    }

    /**
     * @param  string $name
     * @param  mixed  $default
     * @return mixed
     */
    public function getQueryParam($name, $default = null)
    {
        return $this->getQueryParamsContainer()->get($name, $default);
    }

    /**
     * @return array
     */
    public function getAllQueryParams()
    {
        return $this->getQueryParamsContainer()->getAll();
    }

    /**
     * @return array
     */
    public function getQueryParamNames()
    {
        return $this->getQueryParamsContainer()->getNames();
    }

    /**
     * @param  string $name
     * @return boolean
     */
    public function hasQueryParam($name)
    {
        return $this->getQueryParamsContainer()->has($name);
    }

    /**
     * @return boolean
     */
    public function hasAnyQueryParams()
    {
        return $this->getQueryParamsContainer()->hasAny();
    }

    /**
     * @param  string $name
     * @return \Panadas\Http\Request
     */
    public function removeQueryParam($name)
    {
        $this->getQueryParamsContainer()->remove($name);

        return $this;
    }

    /**
     * @param  array $names
     * @return \Panadas\Http\Request
     */
    public function removeManyQueryParams(array $names)
    {
        $this->getQueryParamsContainer()->removeMany($names);

        return $this;
    }

    /**
     * @return \Panadas\Http\Request
     */
    public function removeAllQueryParams()
    {
        $this->getQueryParamsContainer()->removeAll();

        return $this;
    }

    /**
     * @param  string $name
     * @param  mixed  $value
     * @return \Panadas\Http\Request
     */
    public function setQueryParam($name, $value)
    {
        $this->getQueryParamsContainer()->set($name, $value);

        return $this;
    }

    /**
     * @param  array $queryParams
     * @return \Panadas\Http\Request
     */
    public function setManyQueryParams(array $queryParams)
    {
        $this->getQueryParamsContainer()->setMany($queryParams);

        return $this;
    }

    /**
     * @param  array $queryParams
     * @return \Panadas\Http\Request
     */
    public function replaceQueryParams(array $queryParams)
    {
        $this->getQueryParamsContainer()->replace($queryParams);

        return $this;
    }

    /**
     * @param  string $name
     * @param  mixed  $default
     * @return mixed
     */
    public function getDataParam($name, $default = null)
    {
        return $this->getDataParamsContainer()->get($name, $default);
    }

    /**
     * @return array
     */
    public function getAllDataParams()
    {
        return $this->getDataParamsContainer()->getAll();
    }

    /**
     * @return array
     */
    public function getDataParamNames()
    {
        return $this->getDataParamsContainer()->getNames();
    }

    /**
     * @param  string $name
     * @return boolean
     */
    public function hasDataParam($name)
    {
        return $this->getDataParamsContainer()->has($name);
    }

    /**
     * @return boolean
     */
    public function hasAnyDataParams()
    {
        return $this->getDataParamsContainer()->hasAny();
    }

    /**
     * @param  string $name
     * @return \Panadas\Http\Request
     */
    public function removeDataParam($name)
    {
        $this->getDataParamsContainer()->remove($name);

        return $this;
    }

    /**
     * @param  array $names
     * @return \Panadas\Http\Request
     */
    public function removeManyDataParams(array $names)
    {
        $this->getDataParamsContainer()->removeMany($names);

        return $this;
    }

    /**
     * @return \Panadas\Http\Request
     */
    public function removeAllDataParams()
    {
        $this->getDataParamsContainer()->removeAll();

        return $this;
    }

    /**
     * @param  string $name
     * @param  mixed  $value
     * @return \Panadas\Http\Request
     */
    public function setDataParam($name, $value)
    {
        $this->getDataParamsContainer()->set($name, $value);

        return $this;
    }

    /**
     * @param  array $dataParams
     * @return \Panadas\Http\Request
     */
    public function setManyDataParams(array $dataParams)
    {
        $this->getDataParamsContainer()->setMany($dataParams);

        return $this;
    }

    /**
     * @param  array $dataParams
     * @return \Panadas\Http\Request
     */
    public function replaceDataParams(array $dataParams)
    {
        $this->getDataParamsContainer()->replace($dataParams);

        return $this;
    }

    /**
     * @param  string $name
     * @param  mixed  $default
     * @return mixed
     */
    public function getCookie($name, $default = null)
    {
        return $this->getCookiesContainer()->get($name, $default);
    }

    /**
     * @return array
     */
    public function getAllCookies()
    {
        return $this->getCookiesContainer()->getAll();
    }

    /**
     * @return array
     */
    public function getCookieNames()
    {
        return $this->getCookiesContainer()->getNames();
    }

    /**
     * @param  string $name
     * @return boolean
     */
    public function hasCookie($name)
    {
        return $this->getCookiesContainer()->has($name);
    }

    /**
     * @return boolean
     */
    public function hasAnyCookies()
    {
        return $this->getCookiesContainer()->hasAny();
    }

    /**
     * @param  string $name
     * @return \Panadas\Http\Request
     */
    protected function removeCookie($name)
    {
        $this->getCookiesContainer()->remove($name);

        return $this;
    }

    /**
     * @param  array $names
     * @return \Panadas\Http\Request
     */
    protected function removeManyCookies(array $names)
    {
        $this->getCookiesContainer()->removeMany($names);

        return $this;
    }

    /**
     * @return \Panadas\Http\Request
     */
    protected function removeAllCookies()
    {
        $this->getCookiesContainer()->removeAll();

        return $this;
    }

    /**
     * @param  string $name
     * @param  mixed  $value
     * @return \Panadas\Http\Request
     */
    protected function setCookie($name, $value)
    {
        $this->getCookiesContainer()->set($name, $value);

        return $this;
    }

    /**
     * @param  array $cookies
     * @return \Panadas\Http\Request
     */
    protected function setManyCookies(array $cookies)
    {
        $this->getCookiesContainer()->setMany($cookies);

        return $this;
    }

    /**
     * @param  array $cookies
     * @return \Panadas\Http\Request
     */
    protected function replaceCookies(array $cookies)
    {
        $this->getCookiesContainer()->replace($cookies);

        return $this;
    }

    public function getHeader($name, $default = null)
    {
        return $this->getKernel()->getServerVar(static::getPhpHeaderName($name), $default);
    }

    public function hasHeader($name)
    {
        return $this->getKernel()->hasServerVar(static::getPhpHeaderName($name));
    }

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

    public function getMethod()
    {
        return mb_strtoupper(
            $this->get(static::PARAM_METHOD, $this->getKernel()->getServerVar("REQUEST_METHOD", static::METHOD_GET))
        );
    }

    public function isHead()
    {
        return ($this->getMethod() === static::METHOD_HEAD);
    }

    public function isGet()
    {
        return ($this->getMethod() === static::METHOD_GET);
    }

    public function isPost()
    {
        return ($this->getMethod() === static::METHOD_POST);
    }

    public function isPut()
    {
        return ($this->getMethod() === static::METHOD_PUT);
    }

    public function isDelete()
    {
        return ($this->getMethod() === static::METHOD_DELETE);
    }

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

    public function isAjax()
    {
        return (mb_strtoupper($this->getKernel()->getServerVar("HTTP_X_REQUESTED_WITH")) === "XMLHTTPREQUEST");
    }

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
