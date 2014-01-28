<?php
namespace Panadas\Http;

interface KernelAwareInterface
{

    /**
     * @return \Panadas\Http\Kernel
     */
    public function getKernel();
}
