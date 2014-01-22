<?php
namespace Panadas\Service;

abstract class SessionAbstract extends \Panadas\ServiceAbstract implements \SessionHandlerInterface
{

    /**
     * @see    http://php.net/session_set_save_handler
     * @param  string $id
     * @return string
     */
    public abstract function read($id);

    /**
     * @see    http://php.net/session_set_save_handler
     * @param  string $id
     * @param  string $data
     * @return boolean
    */
    public abstract function write($id, $data);

    /**
     * @see    http://php.net/session_set_save_handler
     * @param  string $id
     * @return boolean
    */
    public abstract function destroy($id);

    /**
     * @see    http://php.net/session_set_save_handler
     * @param  integer $lifetime
     * @return boolean
    */
    public abstract function gc($lifetime);

    public function __construct(
        \Panadas\App $app,
        $name = null,
        $lifetime = null,
        $cookie_path = "/",
        $cookie_domain = null
    )
    {
        parent::__construct($app);

        if (ini_get("session.auto_start")) {
            throw new \RuntimeException("PHP INI setting \"session.auto_start\" must be disabled");
        }

        if (!is_null($name)) {
            $this->setName($name);
        }

        if (!is_null($lifetime)) {
            $this->setLifetime($lifetime);
        }

        // Session cookies are only sent over secure connections; helps prevent session hijacking
        // See: http://en.wikipedia.org/wiki/Session_hijacking

        // Session cookies are only sent with the HttpOnly flag; helps prevent cookie-based XSS attacks
        // See: http://en.wikipedia.org/wiki/Cross-site_scripting

        session_set_cookie_params(0, $cookie_path, $cookie_domain, true, true);

        // Prevent session-fixation
        // See: http://en.wikipedia.org/wiki/Session_fixation

        ini_set("session.session.use_only_cookies", 1);

        // Use the SHA-1 hashing algorithm
        ini_set("session.hash_function", 1);

        // Increase character-range of the session ID to help prevent brute-force attacks
        ini_set("session.hash_bits_per_character", 6);

        session_set_save_handler($this);
    }

    public function __toArray()
    {
        return (
            parent::__toArray()
            + [
                "id" => $this->getId(),
                "lifetime" => $this->getLifetime(),
                "params" => $this->getAll()
            ]
        );
    }

    public function __subscribe()
    {
        $events = parent::__subscribe();

        $events["run"][] = function(\Panadas\Event $event) {

            $app = $event->getApp();
            $logger = $app->getServiceContainer()->get("logger", false);

            if (!$event->get("request")->isSecure()) {
                if (!is_null($logger)) {
                    $logger->info("Session not started for unsecure connection");
                }
                return;
            }

            $params = [
                "id" => null
            ];

            $event = $app->publish("session_start", $params);

            $id = $event->get("id");
            if (!is_null($id)) {
                session_id($id);
            }

            session_start();

            if (!is_null($logger)) {
                $logger->info("Session started: {$this->getId()}");
            }

        };

        return $events;
    }

    public function getName()
    {
        return session_name();
    }

    protected function setName($name)
    {
        session_name($name);

        return $this;
    }

    public function getLifetime()
    {
        return ini_get("session.gc_maxlifetime");
    }

    protected function setLifetime($lifetime)
    {
        ini_set("session.gc_maxlifetime", $lifetime);

        return $this;
    }

    public function getHandler()
    {
        return $this->handler;
    }

    public function hasHandler()
    {
        return !is_null($this->getHandler());
    }

    protected function setHandler(\Panadas\Service\Session\HandlerAbstract $handler)
    {
        $this->handler = $handler;

        return $this;
    }

    protected function removeHandler()
    {
        return $this->setHandler(null);
    }

    public function getId()
    {
        $id = session_id();

        return ($id != "") ? $id : null;
    }

    public function setId($id)
    {
        session_id($id);

        return $this;
    }

    public function get($name, $default = null)
    {
        return $this->has($name) ? $_SESSION[$name] : $default;
    }

    public function getAll()
    {
        return $_SESSION;
    }

    public function has($name)
    {
        return array_key_exists($name, $this->getAll());
    }

    public function hasAny()
    {
        return (count($this->getAll()) > 0);
    }

    public function set($name, $value)
    {
        $_SESSION[$name] = $value;

        return $this;
    }

    public function setMany(array $params)
    {
        foreach ($params as $key => $value) {
            $this->set($name, $value);
        }

        return $this;
    }

    public function remove($name)
    {
        if ($this->has($name)) {
            unset($_SESSION[$name]);
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
        return $this->removeMany(array_keys($this->getAll()));
    }

    public function replace(array $params)
    {
        return $this->removeAll()->setMany($params);
    }

    public function destroy()
    {
        $this->removeAll();

        session_regenerate_id(true);

        return $this;
    }

    /**
     * @see    http://php.net/session_set_save_handler
     * @param  string $save_path
     * @param  string $session_name
     * @return boolean
     */
    public function open($save_path, $session_name)
    {
        return true;
    }

    /**
     * @see    http://php.net/session_set_save_handler
     * @return boolean
     */
    public function close()
    {
        return true;
    }

}
