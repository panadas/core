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

    /**
     * @param  string $absolutePath
     * @param  string $rootDir
     * @return string
     */
    public function getRelativePath($absolutePath, $rootDir = null)
    {
        if (null === $rootDir) {
            $rootDir = $this->getRootDir();
        }

        $rootDir = rtrim($rootDir, DIRECTORY_SEPARATOR);
        $rootDirLength = mb_strlen($rootDir);

        if (mb_substr($absolutePath, 0, $rootDirLength) !== $rootDir) {
            throw new \InvalidArgumentException("Absolute path is not within root directory");
        }

        return "." . mb_substr($absolutePath, $rootDirLength);
    }
}
