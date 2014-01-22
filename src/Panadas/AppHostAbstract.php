<?php
namespace Panadas;

abstract class AppHostAbstract extends \Panadas\BaseAbstract
{

    private $app;

    public function __construct(\Panadas\App $app)
    {
        parent::__construct();

        $this->setApp($app);
    }

    public function getApp()
    {
        return $this->app;
    }

    protected function setApp(\Panadas\App $app = null)
    {
        $this->app = $app;

        return $this;
    }

}
