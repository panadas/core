<?php
namespace Panadas\Controller;

abstract class AbstractController extends \Panadas\Http\AbstractKernelAware implements
    \Panadas\Controller\ControllerInterface
{

    private $name;
    private $args = [];

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
            ->replaceArgs($args);
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
     * @param  string $name
     * @param  string $default
     * @return mixed
     */
    public function getArg($name, $default = null)
    {
        return $this->hasArg($name) ? $this->args[$name] : $default;
    }

    /**
     * @return array
     */
    public function getAllArgs()
    {
        return $this->args;
    }

    /**
     * @return array
     */
    public function getArgNames()
    {
        return array_keys($this->getAllArgs());
    }

    /**
     * @param  string $name
     * @return boolean
     */
    public function hasArg($name)
    {
        return array_key_exists($name, $this->getAllArgs());
    }

    /**
     * @return boolean
     */
    public function hasAnyArgs()
    {
        return (count($this->getAllArgs()) > 0);
    }

    /**
     * @param  string $name
     * @return \Panadas\AbstractController
     */
    public function removeArg($name)
    {
        if ($this->hasArg($name)) {
            unset($this->args[$name]);
        }

        return $this;
    }

    /**
     * @param  array $names
     * @return \Panadas\AbstractController
     */
    public function removeManyArgs(array $names)
    {
        foreach ($names as $name) {
            $this->removeArg($name);
        }

        return $this;
    }

    /**
     * @return \Panadas\AbstractController
     */
    public function removeAllArgs()
    {
        return $this->removeManyArgs($this->getArgNames());
    }

    /**
     * @param  array $args
     * @return \Panadas\AbstractController
     */
    public function replaceArgs(array $args)
    {
        return $this->removeAllArgs()->setManyArgs($args);
    }

    /**
     * @param  string $name
     * @param  mixed  $value
     * @return \Panadas\AbstractController
     */
    public function setArg($name, $value)
    {
        $this->args[$name] = $value;

        return $this;
    }

    /**
     * @param  array $args
     * @return \Panadas\AbstractController
     */
    public function setManyArgs(array $args)
    {
        foreach ($args as $name => $value) {
            $this->setArg($name, $value);
        }

        return $this;
    }
}
