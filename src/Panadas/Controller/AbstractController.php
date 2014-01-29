<?php
namespace Panadas\Controller;

abstract class AbstractController extends \Panadas\Http\AbstractKernelAware implements
    \Panadas\Controller\ControllerInterface
{

    private $name;
    private $args;

    abstract public function handle(\Panadas\Http\Request $request);

    /**
     * @param \Panadas\Http\Kernel $kernel
     * @param string               $name
     * @param array                $args
     */
    public function __construct(\Panadas\Http\Kernel $kernel, $name, array $args = [])
    {
        parent::__construct($kernel);

        $this
            ->setName($name)
            ->setArgs(new \Panadas\ArrayStore\HashArrayStore($args));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param  string $name
     * @return \Panadas\AbstractController
     */
    protected function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return \Panadas\ArrayStore\HashArrayStore
     */
    protected function getArgs()
    {
        return $this->args;
    }

    /**
     * @param  \Panadas\ArrayStore\HashArrayStore $args
     * @return \Panadas\Controller\AbstractController
     */
    protected function setArgs(\Panadas\ArrayStore\HashArrayStore $args)
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
