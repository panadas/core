<?php
namespace Panadas\Framework;

class ErrorHandler extends AbstractApplicationAware
{

    public function register()
    {
        ini_set("display_errors", 0);

        set_error_handler([$this, "handle"]);

        register_shutdown_function(function () {

            $error = error_get_last();

            if ((null === $error) || (E_ERROR !== $error["type"])) {
                return;
            }

            $this->handleFatal($error["type"], $error["message"], $error["file"], $error["line"]);

        });

        return $this;
    }

    public function handle($errno, $errstr, $errfile, $errline)
    {
        if (!(error_reporting() & $errno)) {
            return;
        }

        throw new \ErrorException($errstr, 0, $errno, $errfile, $errline);
    }

    public function handleFatal($errno, $errstr, $errfile, $errline)
    {
        if (0 === error_reporting()) {
            return;
        }

        $handler = set_exception_handler(
            function () {
            }
        );
        restore_exception_handler();

        $handler(new \ErrorException($errstr, 0, $errno, $errfile, $errline));
    }
}
