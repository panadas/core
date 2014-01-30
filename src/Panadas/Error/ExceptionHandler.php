<?php
namespace Panadas\Error;

class ExceptionHandler extends \Panadas\Kernel\AbstractKernelAware
{

    /**
     * @return \Panadas\Error\ExceptionHandler
     */
    public function register()
    {
        $actionClass = $this->getActionClass();

        if (!is_subclass_of($actionClass, "Panadas\Error\ExceptionProcessorInterface")) {
            throw new \RuntimeException("{$actionClass} must implement Panadas\Error\ExceptionProcessorInterface");
        }

        set_exception_handler([$this, "handle"]);

        return $this;
    }

    /**
     * @return string
     */
    public function getActionClass()
    {
        $kernel = $this->getKernel();
        return $kernel::ACTION_CLASS_EXCEPTION;
    }

    /**
     * @param \Exception $exception
     */
    public function handle(\Exception $exception)
    {
        $kernel = $this->getKernel();
        $actionClass = $kernel::ACTION_CLASS_EXCEPTION;

        $actionClass::process($kernel, $exception)->send();
    }

}
