<?php
namespace Panadas\Service;

abstract class AuthenticationAbstract extends \Panadas\ServiceAbstract
{

    private $token;
    private $header_name;
    private $cookie_name;
    private $cookie_path;
    private $cookie_domain;

    public abstract function getUser();

    protected abstract function authenticate($username, $password, $persist);

    protected abstract function deauthenticate();

    public function __construct(
        \Panadas\App $app,
        $header_name = "X-Auth-Token",
        $cookie_name = "authtoken",
        $cookie_path = "/",
        $cookie_domain = null
    )
    {
        parent::__construct($app);

        $this
            ->setHeaderName($header_name)
            ->setCookieName($cookie_name)
            ->setCookiePath($cookie_path)
            ->setCookieDomain($cookie_domain);
    }

    public function __subscribe()
    {
        $events = parent::__subscribe();

        $events["run"][] = function(\Panadas\Event $event) {

            $token = $event->get("request")->getHeader(
                $this->getHeaderName(),
                // TODO: Implement cookie handler
                (array_key_exists($this->getCookieName(), $_COOKIE) ? $_COOKIE[$this->getCookieName()] : null)
            );

            if (is_null($token)) {
                return;
            }

            $logger = $this->getApp()->getServiceContainer()->get("logger", false);

            $this->setToken($token);

            if (!is_null($logger)) {
                $logger->info("Authenticating token: {$token}");
            }

            if (!$this->isAuthenticated()) {

                if (!is_null($logger)) {
                    $logger->info("User is not authenticated (invalid or expired token)");
                }

                $this->signOut();

            } else {

                if (!is_null($logger)) {
                    $logger->info("User is authenticated");
                }

            }

        };

        $events["send"][] = function(\Panadas\Event $event) {

            $event->get("response")->setMany(
                [
                    "is_authenticated" => $this->isAuthenticated(),
                    "user" => $this->getUser()
                ]
            );

        };

        return $events;
    }

    public function getToken()
    {
        return $this->token;
    }

    public function hasToken()
    {
        return !is_null($this->getToken());
    }

    protected function setToken($token)
    {
        $this->token = $token;

        return $this;
    }

    protected function removeToken()
    {
        return $this->setToken(null);
    }

    public function getHeaderName()
    {
        return $this->header_name;
    }

    protected function setHeaderName($header_name)
    {
        $this->header_name = $header_name;

        return $this;
    }

    public function getCookieName()
    {
        return $this->cookie_name;
    }

    protected function setCookieName($cookie_name)
    {
        $this->cookie_name = $cookie_name;

        return $this;
    }

    public function getCookiePath()
    {
        return $this->cookie_path;
    }

    protected function setCookiePath($cookie_path)
    {
        $this->cookie_path = $cookie_path;

        return $this;
    }

    public function getCookieDomain()
    {
        return $this->cookie_domain;
    }

    protected function setCookieDomain($cookie_domain)
    {
        $this->cookie_domain = $cookie_domain;

        return $this;
    }

    public function isAuthenticated()
    {
        return ($this->hasToken() && $this->hasUser());
    }

    public function hasUser()
    {
        return !is_null($this->getUser());
    }

    public function signIn($username, $password, $persist = false, $set_cookie = true)
    {
        if ($this->isAuthenticated()) {
            $this->signOut();
        }

        $token = $this->authenticate($username, $password, $persist);

        if (is_null($token)) {
            throw new \Exception("Invalid creditials for user: {$username}");
        }

        $this->setToken($token);

        if ($set_cookie) {
            $this->createCookie();
        }

        return $this;
    }

    public function signOut()
    {
        if ($this->hasToken()) {
            $this->deauthenticate();
        }

        return $this
            ->removeToken()
            ->deleteCookie();
    }

    protected function createCookie()
    {
        return $this->setCookie(null);
    }

    protected function deleteCookie()
    {
        return $this->setCookie(time() - 86400);
    }

    protected function setCookie($expires)
    {
        // TODO: Implement cookie functionality
        setcookie(
            $this->getCookieName(),
            $this->getToken(),
            $expires,
            $this->getCookiePath(),
            $this->getCookieDomain(),
            true,
            true
        );

        return $this;
    }

}
