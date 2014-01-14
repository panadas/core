<?php
namespace Panadas\Service\Authentication;

abstract class LocalAbstract extends \Panadas\Service\AuthenticationAbstract
{

    protected abstract function create($user_id, $lifetime);

    protected abstract function retrieve();

    protected abstract function update(\DateTime $modified);

    protected abstract function delete();

    protected abstract function gc();

    private $user_callback;
    private $user_id_callback;
    private $lifetime;
    private $persist_lifetime;

    public function __construct(
        \Panadas\App $app,
        callable $user_callback,
        callable $user_id_callback,
        $header_name = "X-Auth-Token",
        $cookie_name = "authtoken",
        $cookie_path = "/",
        $cookie_domain = null,
        $lifetime = 10800,
        $persist_lifetime = null
    )
    {
        parent::__construct($app, $header_name, $cookie_name, $cookie_path, $cookie_domain);

        $this
            ->setUserCallback($user_callback)
            ->setUserIdCallback($user_id_callback)
            ->setLifetime($lifetime)
            ->setPersistLifetime($persist_lifetime);
    }

    public function __subscribe()
    {
        $events = parent::__subscribe();

        $events["run"][] = function(\Panadas\Event $event) {

            $this->gc();

        };

        return $events;
    }

    public function getUserCallback()
    {
        return $this->user_callback;
    }

    protected function setUserCallback($user_callback)
    {
        $this->user_callback = $user_callback;

        return $this;
    }

    public function getUserIdCallback()
    {
        return $this->user_id_callback;
    }

    protected function setUserIdCallback($user_id_callback)
    {
        $this->user_id_callback = $user_id_callback;

        return $this;
    }

    public function getLifetime()
    {
        return $this->lifetime;
    }

    protected function setLifetime($lifetime)
    {
        $this->lifetime = (int) $lifetime;

        return $this;
    }

    public function getPersistLifetime()
    {
        return $this->persist_lifetime;
    }

    public function hasPersistLifetime()
    {
        return !is_null($this->getPersistLifetime());
    }

    protected function setPersistLifetime($persist_lifetime)
    {
        if (!is_null($persist_lifetime)) {
            $persist_lifetime = (int) $persist_lifetime;
        }
        $this->persist_lifetime = $persist_lifetime;

        return $this;
    }

    protected function removePersistLifetime()
    {
        return $this->setPersistLifetime(null);
    }

    public function getUser()
    {
        if (!$this->hasToken()) {
            return null;
        }

        $user_id = $this->retrieve();

        $callback = $this->getUserCallback();

        $user = !is_null($user_id) ? $callback($this, $user_id) : null;

        if (!is_null($user)) {

            $this->update(new \DateTime());

        } else {

            $logger = $this->getApp()->getServiceContainer()->get("logger", false);
            if (!is_null($logger)) {
                $logger->warn("Failed to determine user from authentication token: {$this->getToken()}");
            }

            $this->signOut();

        }

        return $user;
    }

    protected function authenticate($username, $password, $persist)
    {
        $callback = $this->getUserIdCallback();

        $user_id = $callback($this, $username, $password);

        if (is_null($user_id)) {
            return null;
        }

        $lifetime = $persist ? $this->getPersistLifetime() : $this->getLifetime();

        return $this->create($user_id, $lifetime);
    }

    protected function deauthenticate()
    {
        return $this->delete();
    }

    protected function generateToken()
    {
        return \Panadas\Util\String::random(40);
    }

}
