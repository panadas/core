<?php
namespace Panadas\Event;

class EventPublisher extends \Panadas\AbstractBase
{

    // TODO: custom array store for event data
    private $events = [];

    /**
     * @param  string   $name
     * @param  callable $listener
     * @param  integer  $priority
     * @return \Panadas\Event\EventPublisher
     */
    public function addListener($name, callable $listener, $priority = 0)
    {
        $this->events[$name][$priority][] = $listener;

        ksort($this->events[$name], SORT_NUMERIC);

        return $this;
    }

    /**
     * @param  string $name
     * @param  array  $listeners
     * @return \Panadas\Event\EventPublisher
     */
    public function addManyListeners($name, array $listeners)
    {
        foreach ($listeners as $config) {

            static::normalizeListenerConfig($config, $listener, $priority);

            $this->addListener($name, $listener, $priority);

        }

        return $this;
    }

    /**
     * @param  string   $name
     * @param  callable $listener
     * @return boolean
     */
    public function hasListener($name, callable $listener)
    {
        foreach ($this->getAllListeners($name) as $priority => $listeners) {
            if (in_array($listeners, $listener)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param  string $name
     * @return boolean
     */
    public function hasAnyListeners($name)
    {
        return (count($this->getAllListeners($name)) > 0);
    }

    /**
     * @param  string $name
     * @return array
     */
    public function getAllListeners($name)
    {
        return array_key_exists($name, $this->events) ? $this->events[$name] : [];
    }

    /**
     * @param  string   $name
     * @param  callable $listener
     * @return \Panadas\Event\EventPublisher
     */
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

    /**
     * @param  string $name
     * @param  array  $listeners
     * @return \Panadas\Event\EventPublisher
     */
    public function removeManyListeners($name, array $listeners)
    {
        foreach ($listeners as $config) {

            static::normalizeListenerConfig($config, $listener, $priority);

            $this->removeListener($name, $listener);

        }

        return $this;
    }

    /**
     * @param  string $name
     * @return \Panadas\Event\EventPublisher
     */
    public function removeAllListeners($name)
    {
        foreach ($this->getAllListeners($name) as $priority => $listeners) {
            $this->removeListener($name, $listener);
        }

        return $this;
    }

    /**
     * @param  string $name
     * @param  array  $listeners
     * @return \Panadas\Event\EventPublisher
     */
    public function replaceListeners($name, array $listeners)
    {
        $this->removeAllListeners($name)->addManyListeners($name, $listeners);

        return $this;
    }

    /**
     * @param  \Panadas\Event\EventSubscriberInterface $subscriber
     * @return \Panadas\Event\EventPublisher
     */
    public function addSubscriber(\Panadas\Event\EventSubscriberInterface $subscriber)
    {
        foreach ($subscriber->subscribe() as $name => $listeners) {

            if (is_callable($listeners)) {
                $this->addListener($name, $listeners);
            } else {
                $this->addManyListeners($name, $listeners);
            }

        }

        return $this;
    }

    /**
     * @param  \Panadas\Event\EventSubscriberInterface $subscriber
     * @return \Panadas\Event\EventPublisher
     */
    public function removeSubscriber(\Panadas\Event\EventSubscriberInterface $subscriber)
    {
        foreach ($subscriber->subscribe() as $name => $listeners) {

            if (is_callable($listeners)) {
                $this->removeListener($name, $listeners);
            } else {
                $this->removeManyListeners($name, $listeners);
            }

        }

        return $this;
    }

    /**
     * @param  string $name
     * @param  array  $params
     * @return \Panadas\Event\Event
     */
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

    /**
     * @param  mixed   $config
     * @param  mixed   $listener
     * @param  integer $priority
     * @throws \Exception
     * @return boolean
     */
    protected static function normalizeListenerConfig($config, &$listener, &$priority)
    {
        $priority = 0;

        if (is_array($config) && (array_key_exists("priority", $config) || array_key_exists("listener", $config))) {

            if (!array_key_exists("listener", $config)) {
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
