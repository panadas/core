<?php
namespace Panadas\Http;

class Request extends \Panadas\Kernel\AbstractKernelAware
{

    private $uri;
    private $headers;
    private $queryParams;
    private $dataParams;
    private $cookies;

    const METHOD_HEAD = "HEAD";
    const METHOD_GET = "GET";
    const METHOD_POST = "POST";
    const METHOD_PUT = "PUT";
    const METHOD_DELETE = "DELETE";

    const PARAM_AJAX = "_ajax";
    const PARAM_METHOD = "_method";

    /**
     * @param \Panadas\Kernel\Kernel $kernel
     * @param array                  $queryParams
     * @param array                  $dataParams
     * @param array                  $cookies
     */
    public function __construct(
        \Panadas\Kernel\Kernel $kernel,
        array $headers = [],
        array $queryParams = [],
        array $dataParams = [],
        array $cookies = []
    ) {
        parent::__construct($kernel);

        $this
            ->setUri($this->detectUri())
            ->setHeaders(new \Panadas\DataStructure\HashDataStructure($headers, false))
            ->setQueryParams(new \Panadas\DataStructure\HashDataStructure($queryParams))
            ->setDataParams(new \Panadas\DataStructure\HashDataStructure($dataParams))
            ->setCookies(new \Panadas\DataStructure\HashDataStructure($cookies));
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
     * @return \Panadas\Http\Request
     */
    protected function setHeaders(\Panadas\DataStructure\HashDataStructure $headers)
    {
        $this->headers = $headers;

        return $this;
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
     * @return \Panadas\DataStructure\HashDataStructure
     */
    protected function getQueryParams()
    {
        return $this->queryParams;
    }

    /**
     * @param  \Panadas\DataStructure\HashDataStructure $queryParams
     * @return \Panadas\Http\Request
     */
    protected function setQueryParams(\Panadas\DataStructure\HashDataStructure $queryParams)
    {
        $this->queryParams = $queryParams;

        return $this;
    }

    /**
     * @return \Panadas\DataStructure\HashDataStructure
     */
    protected function getDataParams()
    {
        return $this->dataParams;
    }

    /**
     * @param  \Panadas\DataStructure\HashDataStructure $dataParams
     * @return \Panadas\Http\Request
     */
    protected function setDataParams(\Panadas\DataStructure\HashDataStructure $dataParams)
    {
        $this->dataParams = $dataParams;

        return $this;
    }

    /**
     * @return \Panadas\DataStructure\HashDataStructure
     */
    protected function getCookies()
    {
        return $this->cookies;
    }

    /**
     * @param  \Panadas\DataStructure\HashDataStructure $cookies
     * @return \Panadas\Http\Request
     */
    protected function setCookies(\Panadas\DataStructure\HashDataStructure $cookies)
    {
        $this->cookies = $cookies;

        return $this;
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
     * @return \Panadas\Http\Request
     */
    public function removeHeader($name)
    {
        $this->getHeaders()->remove($name);

        return $this;
    }

    /**
     * @return \Panadas\Http\Request
     */
    public function removeAllHeaders()
    {
        $this->getHeaders()->removeAll();

        return $this;
    }

    /**
     * @param  string $name
     * @param  mixed  $value
     * @return \Panadas\Http\Request
     */
    public function setHeader($name, $value)
    {
        $this->getHeaders()->set($name, $value);

        return $this;
    }

    /**
     * @param  array $headers
     * @return \Panadas\Http\Request
     */
    public function replaceHeaders(array $headers)
    {
        $this->getHeaders()->replace($headers);

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
     * @return string
     */
    protected function detectUri()
    {
        $kernel = $this->getKernel();
        $secure = $this->isSecure();

        $protocol = $secure ? "https" : "http";

        $port = $kernel->getServerParam("SERVER_PORT");

        if (null !== $port) {
            $port = ($port != ($secure ? 443 : 80)) ? ":{$port}" : null;
        }

        $host = $kernel->getServerParam("HTTP_HOST");
        $path = $kernel->getServerParam("PATH_INFO", $kernel->getServerParam("REQUEST_URI"));
        $query = $kernel->getServerParam("QUERY_STRING");

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
        $method = $this->getQueryParam(static::PARAM_METHOD);
        if (null !== $method) {
            return $method;
        }

        $method = $this->getDataParam(static::PARAM_METHOD);
        if (null !== $method) {
            return $method;
        }

        return $this->getKernel()->getServerParam("REQUEST_METHOD", static::METHOD_GET);
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
            if (mb_strtoupper($kernel->getServerParam($name)) === $value) {
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
        return (($this->getHeader("X-Requested-With") === "XMLHttpRequest")
            || (
                $this->getKernel()->isDebugMode()
                && (
                    $this->hasQueryParam(static::PARAM_AJAX)
                    || $this->hasDataParam(static::PARAM_AJAX)
                )
            )
        );
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

            $value = $kernel->getServerParam($name);

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
     * @param  integer $statusCode
     * @param  string  $message
     * @param  array   $actionArgs
     * @return \Panadas\Http\Response
     */
    public function error($statusCode, $message = null, array $actionArgs = [])
    {
        $kernel = $this->getKernel();
        $actionArgs["statusCode"] = $statusCode;
        $actionArgs["message"] = $message;

        return $this->forward($kernel::ACTION_CLASS_HTTP_ERROR, $actionArgs);
    }

    /**
     * @param  string $message
     * @param  array  $actionArgs
     * @return \Panadas\Http\Response
     */
    public function errorUnauthorized($message = null, array $actionArgs = [])
    {
        return $this->error(401, $message, $actionArgs);
    }

    /**
     * @param  string $message
     * @param  array  $actionArgs
     * @return \Panadas\Http\Response
     */
    public function errorForbidden($message = null, array $actionArgs = [])
    {
        return $this->error(403, $message, $actionArgs);
    }

    /**
     * @param  string $message
     * @param  array  $actionArgs
     * @return \Panadas\Http\Response
     */
    public function errorNotFound($message = null, array $actionArgs = [])
    {
        return $this->error(404, $message, $actionArgs);
    }

    /**
     * @param  string $message
     * @param  array  $actionArgs
     * @return \Panadas\Http\Response
     */
    public function errorBadRequest($message = null, array $actionArgs = [])
    {
        if (null === $message) {
            $message = "HTTP method not supported: {$this->getMethod()}";
        }

        return $this->error(400, $message);
    }

    /**
     * @param  string  $uri
     * @param  integer $statusCode
     * @param  array   $actionArgs
     * @return \Panadas\Http\Response
     */
    public function redirect($uri, $statusCode = 302, array $actionArgs = [])
    {
        $kernel = $this->getKernel();
        $actionArgs["uri"] = $uri;
        $actionArgs["statusCode"] = $statusCode;

        return $this->forward($kernel::ACTION_CLASS_REDIRECT, $actionArgs);
    }

    /**
     * @param  string $actionClass
     * @param  array  $actionArgs
     * @throws \RuntimeException
     * @return \Panadas\Http\Response
     */
    public function forward($actionClass, array $actionArgs = [])
    {
        $kernel = $this->getKernel();

        $params = [
            "request" => $this,
            "response" => null,
            "actionClass" => $actionClass,
            "actionArgs" => $actionArgs
        ];

        $event = $kernel->getEventPublisher()->publish("forward", $params);

        $request = $event->get("request");
        $response = $event->get("response");

        if (null === $response) {

            $actionClass = $event->get("actionClass");
            $actionArgs = $event->get("actionArgs");

            if (!class_exists($actionClass)) {
                throw new \RuntimeException("Action class not found: {$actionClass}");
            }

            $action = new $actionClass($kernel, $actionArgs);

            $response = $action->handle($request);

        }

        return $response;
    }

    /**
     * @param  \Panadas\Kernel\Kernel $kernel
     * @return \Panadas\Http\Request
     */
    public static function create(\Panadas\Kernel\Kernel $kernel)
    {
        $request = new static($kernel, apache_request_headers(), $_GET, $_POST, $_COOKIE);

        if ($request->isPut()) {

            $body = null;
            $params = [];

            $file = fopen("php://input", "r");
            while (!feof($file)) {
                $body .= fread($file, 1024);
            }
            fclose($file);

            parse_str($body, $params);

            foreach ($params as $name => $value) {
                $request->setDataParam($name, $value);
            }

        }

        return $request;
    }
}
