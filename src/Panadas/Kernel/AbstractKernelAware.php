<?php
namespace Panadas\Kernel;

abstract class AbstractKernelAware extends \Panadas\AbstractBase implements \Panadas\Kernel\KernelAwareInterface
{

    use \Panadas\Kernel\KernelAwareTrait;

    /**
     * @param \Panadas\Kernel\AbstractKernel $kernel
     */
    public function __construct(\Panadas\Kernel\AbstractKernel $kernel)
    {
        parent::__construct();

        $this->setKernel($kernel);
    }
}
