<?php
namespace Panadas\Event;

abstract class AbstractEventSubscriber extends \Panadas\Kernel\AbstractKernelAware implements
    \Panadas\Event\EventSubscriberInterface
{

    use \Panadas\Event\EventSubscriberTrait;
}
