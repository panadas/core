<?php
namespace Panadas\Event;

class Publisher extends \Panadas\AbstractBase
{

    private $events = [];

    public function addListener($name, callable $listener, $priority = 0)
    {
        $this->events[$name][$priority][] = $listener;

        ksort($this->events[$name], SORT_NUMERIC);

        return $this;
    }

    public function addManyListeners($name, array $listeners)
    {
        foreach ($listeners as $config) {

            static::normalizeListenerConfig($config, $listener, $priority);

            $this->addListener($name, $listener, $priority);

        }

        return $this;
    }

    public function hasListener($name, callable $listener)
    {
        foreach ($this->getAllListeners($name) as $priority => $listeners) {
            if (in_array($listeners, $listener)) {
                return true;
            }
        }

        return false;
    }

    public function hasAnyListeners($name)
    {
        return (count($this->getAllListeners($name)) > 0);
    }

    public function getAllListeners($name)
    {
        return array_key_exists($name, $this->events) ? $this->events[$name] : [];
    }

    public function removeListener($name, callable $listener)
    {
        foreach ($this->getAllListeners($name) as $priority => $listeners) {

            foreach ($listeners as $listener) {

                $index = array_search($this->events[$name][$priority], $listener);

                if (false !== $index) {
                    unset($this->events[$name][$priority][$index]);
                }

            }

        }

        return $this;
    }

    public function removeManyListeners($name, array $listeners)
    {
        foreach ($listeners as $config) {

            static::normalizeListenerConfig($config, $listener, $priority);

            $this->removeListener($name, $listener);

        }

        return $this;
    }

    public function removeAllListeners($name)
    {
        foreach ($this->getAllListeners($name) as $priority => $listeners) {
            $this->removeListener($name, $listener);
        }

        return $this;
    }

    public function replaceListeners($name, array $listeners)
    {
        $this->removeAllListeners($name)->addManyListeners($name, $listeners);
    }

    public function addSubscriber(\Panadas\Event\SubscriberInterface $subscriber)
    {
        foreach ($subscriber->__subscribe() as $name => $listeners) {

            if (is_callable($listeners)) {
                $this->addListener($name, $listeners);
            } else {
                $this->addManyListeners($name, $listeners);
            }

        }

        return $this;
    }

    public function removeSubscriber(\Panadas\Event\SubscriberInterface $subscriber)
    {
        foreach ($subscriber->__subscribe() as $name => $listeners) {

            if (is_callable($listeners)) {
                $this->removeListener($name, $listeners);
            } else {
                $this->removeManyListeners($name, $listeners);
            }

        }

        return $this;
    }

    public function publish($name, array $params = [])
    {
        $event = new \Panadas\Event\Event($this, $name, $params);

        foreach ($this->getAllListeners($name) as $priority => $listeners) {

            foreach ($listeners as $listener) {

                $listener($event);

                if ($event->isStopped()) {
                    break 2;
                }

            }

        }

        return $event;
    }

    protected static function normalizeListenerConfig($config, &$listener, &$priority)
    {
        $priority = 0;

        if (is_array($config) && (array_key_exists("priority", $config) || array_key_exists("listener", $config))) {

            if ( ! array_key_exists("listener", $config)) {
                throw new \Exception("A listener to must be provided for event: {$name}");
            }

            $listener = $config["listener"];

            if (array_key_exists("priority", $config)) {
                $priority = $config["priority"];
            }

        } else {

            $listener = $config;

        }

        return true;
    }

}
