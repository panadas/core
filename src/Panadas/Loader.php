<?php
namespace Panadas;

class Loader extends \Panadas\AbstractBase
{

    private $rootDir;

    /**
     * @param string $rootDir
     */
    public function __construct($rootDir)
    {
        $this->setRootDir($rootDir);
    }

    /**
     * @return string
     */
    public function getRootDir()
    {
        return $this->rootDir;
    }

    /**
     * @param  string $rootDir
     * @return \Panadas\Loader
     */
    protected function setRootDir($rootDir)
    {
        $this->rootDir = realpath($rootDir);

        return $this;
    }

    /**
     * @param  string $relativePath
     * @param  string $rootDir
     * @return string
     */
    public function getAbsolutePath($relativePath, $rootDir = null)
    {
        if (null === $rootDir) {
            $rootDir = $this->getRootDir();
        }

        return $rootDir . DIRECTORY_SEPARATOR . trim($relativePath, DIRECTORY_SEPARATOR);
    }
}
