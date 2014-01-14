<?php
namespace Panadas;

class ErrorException extends \ErrorException implements \JsonSerializable
{

    public function jsonSerialize()
    {
        return $this->__toArray();
    }

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

    public function getCodeAsString()
    {
        $constants = [
            "E_ERROR",
            "E_WARNING",
            "E_PARSE",
            "E_NOTICE",
            "E_CORE_ERROR",
            "E_CORE_WARNING",
            "E_COMPILE_ERROR",
            "E_COMPILE_WARNING",
            "E_USER_ERROR",
            "E_USER_WARNING",
            "E_USER_NOTICE",
            "E_STRICT",
            "E_RECOVERABLE_ERROR",
            "E_DEPRECATED",
            "E_USER_DEPRECATED"
        ];

        $code = parent::getCode();

        if ($code > 0) {
            foreach ($constants as $constant) {
                if ($code == constant($constant)) {
                    return $constant;
                }
            }
        }

        return "UNKNOWN";
    }

}
