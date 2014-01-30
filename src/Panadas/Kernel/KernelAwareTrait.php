<?php
namespace Panadas\Kernel;

trait KernelAwareTrait
{

    private $kernel;

    /**
     * @return \Panadas\Kernel\AbstractKernel
     */
    public function getKernel()
    {
        return $this->kernel;
    }

    /**
     * @param  \Panadas\Kernel\AbstractKernel $kernel
     * @return mixed
     */
    protected function setKernel(\Panadas\Kernel\AbstractKernel $kernel = null)
    {
        $this->kernel = $kernel;

        return $this;
    }
}
