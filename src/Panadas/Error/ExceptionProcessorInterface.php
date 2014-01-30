<?php
namespace Panadas\Error;

interface ExceptionProcessorInterface
{

    /**
     * @param  \Panadas\Kernel\Kernel $kernel
     * @param  \Exception             $exception
     * @return \Panadas\Http\Response
     */
    public static function process(\Panadas\Kernel\Kernel $kernel, \Exception $exception);
}
