<?php
namespace Panadas;

class AppContainer extends \Panadas\AbstractBase
{

    private $app;

    /**
     * @param \Panadas\App $app
     */
    public function __construct(\Panadas\App $app)
    {
        parent::__construct();

        $this->setApp($app);
    }

    /**
     * @return \Panadas\App
     */
    public function getApp()
    {
        return $this->app;
    }

    /**
     * @param  \Panadas\App $app
     * @return \Panadas\AbstractAppContainer
     */
    protected function setApp(\Panadas\App $app = null)
    {
        $this->app = $app;

        return $this;
    }

}
