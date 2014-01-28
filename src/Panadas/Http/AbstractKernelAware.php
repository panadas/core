<?php
namespace Panadas\Http;

abstract class AbstractKernelAware extends \Panadas\AbstractBase implements \Panadas\Http\KernelAwareInterface
{

    use \Panadas\Http\KernelAwareTrait;

    /**
     * @param \Panadas\Http\Kernel $kernel
     */
    public function __construct(\Panadas\Http\Kernel $kernel)
    {
        parent::__construct();

        $this->setKernel($kernel);
    }
}
