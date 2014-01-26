<?php
namespace Panadas;

class Loader extends \Panadas\AbstractBase
{

    private $root_dir;

    const DIR_VIEWS_ACTIONS = "actions";
    const DIR_VIEWS_FRAGMENTS = "fragments";

    public function __construct($root_dir)
    {
        $this->setRootDir($root_dir);
    }

    public function getRootDir()
    {
        return $this->root_dir;
    }

    protected function setRootDir($root_dir)
    {
        $this->root_dir = realpath($root_dir);

        return $this;
    }

    public function getAbsolutePath($relative_path, $root_dir = null)
    {
        if (is_null($root_dir)) {
            $root_dir = $this->getRootDir();
        }

        $relative_path = trim($relative_path, "/");

        return "{$root_dir}/{$relative_path}";
    }

    public function getActionViewPaths($filename = null)
    {
        return $this->getViewPaths(static::DIR_VIEWS_ACTIONS, $filename);
    }

    public function getFragmentViewPaths($filename = null)
    {
        return $this->getViewPaths(static::DIR_VIEWS_FRAGMENTS, $filename);
    }

    public function getViewPaths($dir = null, $filename = null)
    {
        $relative_path = "views";

        if (!is_null($dir)) {
            $relative_path .= "/{$dir}";
        }

        if (!is_null($filename)) {
            $relative_path .= "/{$filename}";
        }

        // TODO: plugin views

        return [
            $this->getAbsolutePath($relative_path)
        ];
    }

    public function getActionViewContent($filename, array $vars = [])
    {
        return $this->getViewContent(static::DIR_VIEWS_ACTIONS, $filename, $vars);
    }

    public function getFragmentViewContent($filename, array $vars = [])
    {
        return $this->getViewContent(static::DIR_VIEWS_FRAGMENTS, $filename, $vars);
    }

    protected function getViewContent($dir, $filename, array $vars = [])
    {
        foreach ($this->getViewPaths($dir, $filename) as $path) {

            if (!file_exists($path)) {
                continue;
            }

            return $this->import($path, false, $vars, true);

        }

        return null;
    }

    public function import($path, $once = false, array $vars = [], $contents = false)
    {
        if ( ! file_exists($path)) {
            throw new \InvalidArgumentException("File does not exist: {$path}");
        }

        $import = function($_path, $_once, $_vars, $_contents) {

            extract($_vars);

            if ($_contents) {
                ob_start();
            }

            $result = $_once ? require_once $_path : require $_path;

            return $_contents ? ob_get_clean() : $result;

        };

        return $import($path, $once, $vars, $contents);
    }

}
