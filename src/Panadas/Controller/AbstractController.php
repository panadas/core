<?php
namespace Panadas\Controller;

abstract class AbstractController extends \Panadas\Http\AbstractKernelAware implements
    \Panadas\Controller\ControllerInterface
{

    private $name;
    private $argsContainer;

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
            ->setArgsContainer(new \Panadas\ParamContainer($args));
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
     * @return \Panadas\ParamContainer
     */
    protected function getArgsContainer()
    {
        return $this->argsContainer;
    }

    /**
     * @param  \Panadas\ParamContainer $argsContainer
     * @return \Panadas\Controller\AbstractController
     */
    protected function setArgsContainer(\Panadas\ParamContainer $argsContainer)
    {
        $this->argsContainer = $argsContainer;

        return $this;
    }

    /**
     * @param  string $name
     * @param  mixed  $default
     * @return mixed
     */
    public function getArg($name, $default = null)
    {
        return $this->getArgsContainer()->get($name, $default);
    }

    /**
     * @return array
     */
    public function getAllArgs()
    {
        return $this->getArgsContainer()->getAll();
    }

    /**
     * @return array
     */
    public function getArgNames()
    {
        return $this->getArgsContainer()->getNames();
    }

    /**
     * @param  string $name
     * @return boolean
     */
    public function hasArg($name)
    {
        return $this->getArgsContainer()->has($name);
    }

    /**
     * @return boolean
     */
    public function hasAnyArgs()
    {
        return $this->getArgsContainer()->hasAny();
    }

    /**
     * @param  string $name
     * @return \Panadas\Controller\AbstractController
     */
    public function removeArg($name)
    {
        $this->getArgsContainer()->remove($name);

        return $this;
    }

    /**
     * @param  array $names
     * @return \Panadas\Controller\AbstractController
     */
    public function removeManyArgs(array $names)
    {
        $this->getArgsContainer()->removeMany($names);

        return $this;
    }

    /**
     * @return \Panadas\Controller\AbstractController
     */
    public function removeAllArgs()
    {
        $this->getArgsContainer()->removeAll();

        return $this;
    }

    /**
     * @param  string $name
     * @param  mixed  $value
     * @return \Panadas\Controller\AbstractController
     */
    public function setArg($name, $value)
    {
        $this->getArgsContainer()->set($name, $value);

        return $this;
    }

    /**
     * @param  array $args
     * @return \Panadas\Controller\AbstractController
     */
    public function setManyArgs(array $args)
    {
        $this->getArgsContainer()->setMany($args);

        return $this;
    }

    /**
     * @param  array $args
     * @return \Panadas\Controller\AbstractController
     */
    public function replaceArgs(array $args)
    {
        $this->getArgsContainer()->replace($args);

        return $this;
    }
}
