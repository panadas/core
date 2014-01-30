<?php
namespace Panadas\Controller;

abstract class AbstractController extends \Panadas\Kernel\AbstractKernelAware
{

    private $args;

    /**
     * @param  \Panadas\Http\Request $request
     * @return \Panadas\Http\Response
     */
    abstract public function handle(\Panadas\Http\Request $request);

    /**
     * @param \Panadas\Kernel\Kernel $kernel
     * @param array                  $args
     */
    public function __construct(\Panadas\Kernel\Kernel $kernel, array $args = [])
    {
        parent::__construct($kernel);

        $this->setArgs(new \Panadas\DataStructure\HashDataStructure($args));
    }

    /**
     * @return \Panadas\DataStructure\HashDataStructure
     */
    protected function getArgs()
    {
        return $this->args;
    }

    /**
     * @param  \Panadas\DataStructure\HashDataStructure $args
     * @return \Panadas\Controller\AbstractController
     */
    protected function setArgs(\Panadas\DataStructure\HashDataStructure $args)
    {
        $this->args = $args;

        return $this;
    }

    /**
     * @param  string $name
     * @param  mixed  $default
     * @return mixed
     */
    public function getArg($name, $default = null)
    {
        return $this->getArgs()->get($name, $default);
    }

    /**
     * @return array
     */
    public function getAllArgs()
    {
        return $this->getArgs()->getAll();
    }

    /**
     * @return array
     */
    public function getArgNames()
    {
        return $this->getArgs()->getNames();
    }

    /**
     * @param  string $name
     * @return boolean
     */
    public function hasArg($name)
    {
        return $this->getArgs()->has($name);
    }

    /**
     * @return boolean
     */
    public function hasAnyArgs()
    {
        return $this->getArgs()->hasAny();
    }

    /**
     * @param  string $name
     * @return \Panadas\Controller\AbstractController
     */
    public function removeArg($name)
    {
        $this->getArgs()->remove($name);

        return $this;
    }

    /**
     * @return \Panadas\Controller\AbstractController
     */
    public function removeAllArgs()
    {
        $this->getArgs()->removeAll();

        return $this;
    }

    /**
     * @param  string $name
     * @param  mixed  $value
     * @return \Panadas\Controller\AbstractController
     */
    public function setArg($name, $value)
    {
        $this->getArgs()->set($name, $value);

        return $this;
    }

    /**
     * @param  array $args
     * @return \Panadas\Controller\AbstractController
     */
    public function replaceArgs(array $args)
    {
        $this->getArgs()->replace($args);

        return $this;
    }
}
