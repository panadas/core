<?php
namespace Panadas\Kernel;

interface KernelAwareInterface
{

    /**
     * @return \Panadas\Kernel\AbstractKernel
     */
    public function getKernel();
}
