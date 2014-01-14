<?php
namespace Panadas\Service\Router;

class Route extends \Panadas\BaseAbstract
{

    private $router;
    private $name;
    private $pattern;
    private $action;
    private $pattern_regexp;
    private $pattern_params = [];
    private $action_args = [];

    const PATTERN_PARAM_REGEXP = "[a-z0-9_]+";
    const PATTERN_PARAM_REGEXP_DEFAULT = "[^/]+";

    public function __construct(
        \Panadas\Service\Router $router,
        $name,
        $pattern,
        $action,
        array $pattern_param_values = [],
        array $pattern_param_regexps = [],
        array $action_args = []
    )
    {
        parent::__construct();

        $this
            ->setRouter($router)
            ->setName($name)
            ->setPattern($pattern, $pattern_param_values, $pattern_param_regexps)
            ->setAction($action)
            ->replaceActionArgs($action_args);
    }

    public function __toArray()
    {
        return (
            parent::__toArray()
            + [
                "name" => $this->getName(),
                "pattern" => $this->getPattern()
            ]
        );
    }

    public function getRouter()
    {
        return $this->router;
    }

    protected function setRouter(\Panadas\Service\Router $router)
    {
        $this->router = $router;
        return $this;
    }

    public function getName()
    {
        return $this->name;
    }

    protected function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    public function getAction()
    {
        return $this->action;
    }

    protected function setAction($action)
    {
        $this->action = $action;

        return $this;
    }

    public function getPattern()
    {
        return $this->pattern;
    }

    protected function setPattern($pattern, array $param_values = [], array $param_regexps = [])
    {
        $this->pattern = $pattern;

        $pattern_params = [];

        foreach ($this->extractPatternParams() as $name) {

            $data = [];

            if (array_key_exists($name, $param_values)) {
                $data["value"] = $param_values[$name];
            }

            if (array_key_exists($name, $param_regexps)) {
                $data["regexp"] = $param_regexps[$name];
            }

            $pattern_params[$name] = $data;
        }

        return $this
            ->replacePatternParams($pattern_params)
            ->setPatternRegexp($this->extractPatternRegexp());
    }

    public function getPatternRegexp()
    {
        return $this->pattern_regexp;
    }

    protected function setPatternRegexp($pattern_regexp)
    {
        $this->pattern_regexp = $pattern_regexp;

        return $this;
    }

    public function getActionArg($name, $default = null)
    {
        return $this->hasActionArg($name) ? $this->action_args[$name] : $default;
    }

    public function getAllActionArgs()
    {
        return $this->action_args;
    }

    public function getActionArgNames()
    {
        return array_keys($this->getAllActionArgs());
    }

    public function hasActionArg($name)
    {
        return array_key_exists($name, $this->getAllActionArgs());
    }

    public function hasAnyActionArgs()
    {
        return (count($this->getAllActionArgs()) > 0);
    }

    public function removeActionArg($name)
    {
        if ($this->hasActionArg($name)) {
            unset($this->action_args[$name]);
        }

        return $this;
    }

    public function removeManyActionArgs(array $names)
    {
        foreach ($names as $name) {
            $this->removeActionArg($name);
        }

        return $this;
    }

    public function removeAllActionArgs()
    {
        return $this->removeManyActionArgs($this->getActionArgNames());
    }

    public function replaceActionArgs(array $action_args)
    {
        return $this->removeAllActionArgs()->setManyActionArgs($action_args);
    }

    public function setActionArg($name, $value)
    {
        $this->action_args[$name] = $value;

        return $this;
    }

    public function setManyActionArgs(array $action_args)
    {
        foreach ($action_args as $name => $value) {
            $this->setActionArg($name, $value);
        }

        return $this;
    }

    public function getPatternParam($name)
    {
        return $this->hasPatternParam($name) ? $this->params[$name] : null;
    }

    public function getPatternParamValue($name, $default = null)
    {
        return $this->hasPatternParam($name) ? $this->params[$name]["value"] : $default;
    }

    public function getPatternParamRegexp($name, $default = self::PATTERN_PARAM_REGEXP_DEFAULT)
    {
        return $this->hasPatternParam($name) ? $this->params[$name]["regexp"] : $default;
    }

    public function getAllPatternParams()
    {
        return $this->pattern_params;
    }

    public function getPatternParamNames()
    {
        return array_keys($this->getAllPatternParams());
    }

    public function hasPatternParam($name)
    {
        return array_key_exists($name, $this->getAllPatternParams());
    }

    public function hasAnyPatternParams()
    {
        return (count($this->getAllPatternParams()) > 0);
    }

    public function removePatternParam($name)
    {
        if ($this->hasPatternParam($name)) {
            unset($this->pattern_params[$name]);
        }

        return $this;
    }

    public function removeManyPatternParams(array $names)
    {
        foreach ($names as $name) {
            $this->removePatternParam($name);
        }

        return $this;
    }

    public function removeAllPatternParams()
    {
        return $this->removeManyPatternParams($this->getPatternParamNames());
    }

    public function replacePatternParams(array $pattern_params)
    {
        return $this->removeAllPatternParams()->setManyPatternParams($pattern_params);
    }

    public function setPatternParam($name, $value = null, $regexp = self::PATTERN_PARAM_REGEXP_DEFAULT)
    {
        $this->pattern_params[$name] = [
            "value" => $value,
            "regexp" => $regexp
        ];

        return $this;
    }

    public function setManyPatternParams(array $pattern_params)
    {
        foreach ($pattern_params as $name => $data) {

            $has_value = array_key_exists("value", $data);
            $has_regexp = array_key_exists("regexp", $data);

            if ($has_value && $has_regexp) {
                $this->setPatternParam($name, $data["value"], $data["regexp"]);
            } elseif ($has_value) {
                $this->setPatternParam($name, $data["value"]);
            } elseif ($has_regexp) {
                $this->setPatternParam($name, null, $data["regexp"]);
            } else {
                $this->setPatternParam($name);
            }

        }
        return $this;
    }

    public function generateUri(array $pattern_param_values = [])
    {
        $search_replace = [];

        foreach ($this->getAllPatternParams() as $name => $data) {

            if (array_key_exists($name, $pattern_param_values)) {
                $value = $pattern_param_values[$name];
                unset($pattern_param_values[$name]);
            } elseif (null !== $data["value"]) {
                $value = $data["value"];
            } else {
                "A value for \"{$name}\" must be provided to generate a URI for route: {$this->getName()}";
            }

            $search_replace["/:" . preg_quote($name, "/") . "/i"] = $value;
        }

        $uri = preg_replace(array_keys($search_replace), $search_replace, $this->getPattern());

        if (count($pattern_param_values) > 0) {
            $uri .= "?" . http_build_query($pattern_param_values);
        }

        return $uri;
    }

    protected function extractPatternRegexp()
    {
        $search_replace = [];

        foreach ($this->getAllPatternParams() as $name => $data) {

            $name_quoted = preg_quote($name, "/");
            $search_replace["/:{$name_quoted}/i"] = "(?<{$name_quoted}>{$data["regexp"]})";

        }

        $pattern = $this->getPattern();
        $is_folder = (substr_count($pattern, ".") === 0);

        if ($is_folder) {
            $pattern = rtrim($pattern, "/");
        }

        $regexp = preg_replace(array_keys($search_replace), $search_replace, $pattern);

        if ($is_folder) {
            $regexp .= "/?";
        }

        return $regexp;
    }

    protected function extractPatternParams()
    {
        preg_match_all("/:(" . static::PATTERN_PARAM_REGEXP . ")/i", $this->getPattern(), $matches);

        return $matches[1];
    }

}
