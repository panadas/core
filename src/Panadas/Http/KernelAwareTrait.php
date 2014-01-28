<?php
namespace Panadas\Http;

trait KernelAwareTrait
{

    private $kernel;

    /**
     * @return \Panadas\Http\Kernel
     */
    public function getKernel()
    {
        return $this->kernel;
    }

    /**
     * @param  \Panadas\Http\Kernel $kernel
     * @return mixed
     */
    protected function setKernel(\Panadas\Http\Kernel $kernel = null)
    {
        $this->kernel = $kernel;

        return $this;
    }
}
