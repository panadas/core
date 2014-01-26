<?php
namespace Panadas;

class Loader extends \Panadas\AbstractBase
{

    private $root_dir;

    /**
     * @param string $root_dir
     */
    public function __construct($root_dir)
    {
        $this->setRootDir($root_dir);
    }

    /**
     * @return string
     */
    public function getRootDir()
    {
        return $this->root_dir;
    }

    /**
     * @param  string $root_dir
     * @return \Panadas\Loader
     */
    protected function setRootDir($root_dir)
    {
        $this->root_dir = realpath($root_dir);

        return $this;
    }

    /**
     * @param  string $relative_path
     * @param  string $root_dir
     * @return string
     */
    public function getAbsolutePath($relative_path, $root_dir = null)
    {
        if (null === $root_dir) {
            $root_dir = $this->getRootDir();
        }

        return $root_dir . DIRECTORY_SEPARATOR . trim($relative_path, DIRECTORY_SEPARATOR);
    }

}
