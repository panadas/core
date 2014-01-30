<?php
namespace Panadas\Kernel;

interface KernelAwareInterface
{

    /**
     * @return \Panadas\Kernel\Kernel
     */
    public function getKernel();
}
