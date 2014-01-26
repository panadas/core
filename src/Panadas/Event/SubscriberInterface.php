<?php
namespace Panadas\Event;

interface SubscriberInterface
{

    /**
     * @return array
     */
    public function __subscribe();

}
