<?php
namespace Panadas;

class ErrorException extends \ErrorException implements \JsonSerializable
{

    /**
     * @see JsonSerializable::jsonSerialize()
     * @return array
     */
    public function jsonSerialize()
    {
        return $this->__toArray();
    }

    /**
     * @return array
     */
    public function __toArray()
    {
        return [
            "code" => $this->getCode(),
            "code_string" => $this->getCodeAsString(),
            "severity" => $this->getSeverity(),
            "message" => $this->getMessage(),
            "file" => $this->getFile(),
            "line" => $this->getLine(),
            "trace" => $this->getTrace()
        ];
    }

    /**
     * @return string
     */
    public function getCodeAsString()
    {
        $map = [
            E_ERROR => "E_ERROR",
            E_WARNING => "E_WARNING",
            E_PARSE => "E_PARSE",
            E_NOTICE => "E_NOTICE",
            E_CORE_ERROR => "E_CORE_ERROR",
            E_CORE_WARNING => "E_CORE_WARNING",
            E_COMPILE_ERROR => "E_COMPILE_ERROR",
            E_COMPILE_WARNING => "E_COMPILE_WARNING",
            E_USER_ERROR => "E_USER_ERROR",
            E_USER_WARNING => "E_USER_WARNING",
            E_USER_NOTICE => "E_USER_NOTICE",
            E_STRICT => "E_STRICT",
            E_RECOVERABLE_ERROR => "E_RECOVERABLE_ERROR",
            E_DEPRECATED => "E_DEPRECATED",
            E_USER_DEPRECATED => "E_USER_DEPRECATED",
        ];

        $code = $this->getCode();

        if ( ! array_key_exists($code, $map)) {
            return null;
        }

        return $map[$code];
    }

}
