<?php
namespace Panadas\Error;

class ErrorHandler extends \Panadas\Http\AbstractKernelAware
{

    /**
     * @return \Panadas\Error\ErrorHandler
     */
    public function register()
    {
        set_error_handler([$this, "handle"]);

        register_shutdown_function(
            function () {

                $error = error_get_last();

                if ((0 === error_reporting()) || (null === $error) || (E_ERROR !== $error["type"])) {
                    return;
                }

                $this->handle($error["type"], $error["message"], $error["file"], $error["line"]);

            }
        );

        return $this;
    }

    /**
     * @param  integer $errno
     * @param  string  $errstr
     * @param  string  $errfile
     * @param  integer $errline
     * @throws \ErrorException
     */
    public function handle($errno, $errstr, $errfile, $errline)
    {
        if (!(error_reporting() & $errno)) {
            return;
        }

        throw new \ErrorException($errstr, 0, $errno, $errfile, $errline);
    }
}
