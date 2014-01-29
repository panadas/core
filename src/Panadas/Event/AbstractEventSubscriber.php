<?php
namespace Panadas\Event;

abstract class AbstractEventSubscriber extends \Panadas\Http\AbstractKernelAware implements
    \Panadas\Event\EventSubscriberInterface
{

    use \Panadas\Event\EventSubscriberTrait;
}
