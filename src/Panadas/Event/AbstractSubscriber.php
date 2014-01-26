<?php
namespace Panadas\Event;

abstract class AbstractSubscriber extends \Panadas\Http\AbstractKernelAware implements \Panadas\Event\SubscriberInterface
{

    use \Panadas\Event\SubscriberTrait;

}
