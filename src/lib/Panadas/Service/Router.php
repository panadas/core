<?php
namespace Panadas\Service;

class Router extends \Panadas\ServiceAbstract
{

    private $current;
    private $routes = [];

    public function __toArray()
    {
        $routes = [];

        foreach ($this->getAll() as $name => $route) {
            $routes[$name] = $route->__toArray();
        }

        return (
            parent::__toArray()
            + [
                "routes" => $routes
            ]
        );
    }

    public function __subscribe()
    {
        $events = parent::__subscribe();

        $events["run"][] = function(\Panadas\Event $event) {

            if (!is_null($event->get("action_name"))) {
                return null;
            }

            $app = $event->getApp();
            $logger = $app->getServiceContainer()->get("logger", false);

            $request = $event->get("request");
            $method = $request->getMethod();
            $uri = $request->getUri(false, false);

            $route = $this->findByUri($uri);

            if (is_null($route)) {
                if (!is_null($logger)) {
                    $logger->warn("Route not found for request: {$method} {$uri})");
                }
                return null;
            }

            foreach ($route->getAllPatternParams() as $name => $data) {
                $request->set($name, $data["value"]);
            }

            if (!is_null($logger)) {
                $logger->info("Route \"{$route->getName()}\" matched for request: {$method} {$uri}");
            }

            $this->setCurrent($route);

            $event->set("action_name", $route->getAction());
            $event->set("action_args", $route->getAllActionArgs());

        };

        return $events;
    }

    public function add(\Panadas\Service\Router\Route $route)
    {
        $this->routes[$route->getName()] = $route;

        return $this;
    }

    public function addMany(array $routes)
    {
        foreach ($routes as $route) {
            $this->add($route);
        }

        return $this;
    }

    public function get($name)
    {
        return $this->has($name) ? $this->routes[$name] : null;
    }

    public function getAll()
    {
        return $this->routes;
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
            unset($this->routes[$name]);
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

    public function replace(array $routes)
    {
        return $this->removeAll()->addMany($routes);
    }

    public function getCurrent()
    {
        return $this->current;
    }

    public function hasCurrent()
    {
        return !is_null($this->getCurrent());
    }

    protected function setCurrent(\Panadas\Service\Router\Route $current = null)
    {
        $this->current = $current;

        return $this;
    }

    protected function removeCurrent()
    {
        return $this->setCurrent(null);
    }

    public function findByUri($uri)
    {
        $values = [];
        $matched = false;

        foreach ($this->getAll() as $route) {

            $regexp = str_replace("/", "\\/", $route->getPatternRegexp());

            if (preg_match("/^{$regexp}$/", $uri, $values)) {
                $matched = true;
                break;
            }

        }

        if ( ! $matched) {
            return null;
        }

        $route = clone $route;

        foreach ($route->getPatternParamNames() as $name) {
            $route->setPatternParam($name, $values[$name]);
        }

        return $route;
    }

    public function generateUri($name, array $placeholders = [])
    {
        $route = $this->get($name);

        if (null === $route) {
            throw new \RuntimeException("Cannot generate URI for undefined route: {$name}");
        }

        return $route->generateUri($placeholders);
    }

}
