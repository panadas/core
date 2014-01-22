<?php
namespace Panadas\Service;

class Csrf extends \Panadas\ServiceAbstract
{

    private $token;

    const SESSION_KEY = "_csrf";
    const REQUEST_KEY = "_csrf";
    const TOKEN_LENGTH = 10;

    public function __construct(\Panadas\App $app)
    {
        parent::__construct($app);

        $this->setToken(\Panadas\Util\String::random(static::TOKEN_LENGTH));
    }

    public function getSalt()
    {
        return $this->salt;
    }

    protected function setSalt($salt)
    {
        $this->salt = $salt;

        return $this;
    }

    public function getToken()
    {
        return $this->token;
    }

    protected function setToken($token)
    {
        $this->token = $token;

        return $this;
    }

    public function isValid(\Panadas\Request $request)
    {
        $session = $this->getApp()->getServiceContainer()->get("session");

        return ($request->get(static::REQUEST_KEY) == $session->get(static::SESSION_KEY));
    }

    public function validate(\Panadas\Request $request)
    {
        if (!$this->isValid($request)) {
            return $this->getApp()->error400("Your browser sent an invalid request (bad CSRF token).");
        }

        return true;
    }

}
