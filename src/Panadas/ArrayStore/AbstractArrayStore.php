<?php
namespace Panadas\ArrayStore;

abstract class AbstractArrayStore extends \Panadas\AbstractBase
{

    protected $params = [];

    /**
     * @param  array $params
     * @return \Panadas\ArrayStore\AbstractArrayStore
     */
    abstract public function replace(array $params);

    /**
     * @return \Panadas\ArrayStore\AbstractArrayStore
     */
    abstract public function removeAll();

    /**
     * @param array $params
     */
    public function __construct(array $params = [])
    {
        parent::__construct();

        $this->replace($params);
    }

    /**
     * @return array
     */
    public function getAll()
    {
        return $this->params;
    }

    /**
     * @return boolean
     */
    public function hasAny()
    {
        return (count($this->getAll()) > 0);
    }
}
