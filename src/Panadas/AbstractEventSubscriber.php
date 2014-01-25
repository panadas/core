<?php
namespace Panadas;

abstract class AbstractEventSubscriber extends \Panadas\AppContainer
{

    /**
     * @return array
     */
    public function __subscribe()
    {
        return [];
    }

}
