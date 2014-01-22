<?php
namespace Panadas;

class Request extends \Panadas\AppHostAbstract
{

    private $uri;
    private $params = [];

    const METHOD_HEAD   = "HEAD";
    const METHOD_GET    = "GET";
    const METHOD_POST   = "POST";
    const METHOD_PUT    = "PUT";
    const METHOD_DELETE = "DELETE";

    const METHOD_PARAM = "_method";

    public function __construct(\Panadas\App $app, array $params = [])
    {
        parent::__construct($app);

        $this
            ->setUri($this->detectUri())
            ->replace($params);
    }

    public function __toArray()
    {
        return (
    	    parent::__toArray()
            + [
    	        "params" => $this->getAll()
            ]
        );
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

    public function get($name, $default = null)
    {
        return $this->has($name) ? $this->params[$name] : $default;
    }

    public function getAll()
    {
        return $this->params;
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
            unset($this->params[$name]);
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

    public function replace(array $params)
    {
        return $this->removeAll()->setMany($params);
    }

    public function set($name, $value)
    {
        $this->params[$name] = $value;

        return $this;
    }

    public function setMany(array $params)
    {
        foreach ($params as $name => $value) {
            $this->set($name, $value);
        }

        return $this;
    }

    public function getHeader($name, $default = null)
    {
        return $this->getApp()->getServerVar(\Panadas\Util\Http::getPhpHeaderName($name), $default);
    }

    public function hasHeader($name)
    {
        return $this->getApp()->hasServerVar(\Panadas\Util\Http::getPhpHeaderName($name));
    }

    protected function detectUri()
    {
        $app = $this->getApp();
        $is_secure = $this->isSecure();
        $protocol = $is_secure ? "https" : "http";

        $port = $app->getServerVar("SERVER_PORT");

        if (!is_null($port)) {
            $port = ($port != ($is_secure ? 443 : 80)) ? ":{$port}" : null;
        }

        $host = $app->getServerVar("HTTP_HOST");
        $path = $app->getServerVar("PATH_INFO", $app->getServerVar("REQUEST_URI"));
        $query = $app->getServerVar("QUERY_STRING");

        if (mb_strlen($query) > 0) {
            $query = "?{$query}";
        }

        return "{$protocol}://{$host}{$port}{$path}{$query}";
    }

    public function getMethod()
    {
        return mb_strtoupper(
            $this->get(static::METHOD_PARAM, $this->getApp()->getServerVar("REQUEST_METHOD", static::METHOD_GET))
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
        $app = $this->getApp();

        $header_map = [
            "HTTPS" => "ON",
            "HTTP_X_FORWARDED_PROTO" => "HTTPS"
        ];

        foreach ($header_map as $name => $value) {

            if (mb_strtoupper($app->getServerVar($name)) === $value) {
                return true;
            }

        }

        return false;
    }

    public function isAjax()
    {
        return (mb_strtoupper($this->getApp()->getServerVar("HTTP_X_REQUESTED_WITH")) === "XMLHTTPREQUEST");
    }

    public function getIp()
    {
        $app = $this->getApp();

        $header_map = [
            "HTTP_CLIENT_IP",
            "HTTP_X_FORWARDED_FOR",
            "REMOTE_ADDR"
        ];

        foreach ($header_map as $name) {

            $value = $app->getServerVar($name);

            if (is_null($value)) {
                continue;
            }

            if (mb_strpos($value, ",") !== false) {
                $value = trim(explode(",", $value)[0]);
            }

            return $value;

        }

        return null;
    }

}
