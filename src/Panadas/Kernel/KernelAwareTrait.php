<?php
namespace Panadas\Kernel;

trait KernelAwareTrait
{

    private $kernel;

    /**
     * @return \Panadas\Kernel\Kernel
     */
    public function getKernel()
    {
        return $this->kernel;
    }

    /**
     * @param  \Panadas\Kernel\Kernel $kernel
     * @return mixed
     */
    protected function setKernel(\Panadas\Kernel\Kernel $kernel = null)
    {
        $this->kernel = $kernel;

        return $this;
    }
}
