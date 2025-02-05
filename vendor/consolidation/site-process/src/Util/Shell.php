<?php
namespace Consolidation\SiteProcess\Util;

/**
 * Shell::op is a static factory that will create shell operators for use
 * in command line arguments list. Shell operators are characters that have
 * special meaning to the shell, such as "output redirection". When a shell
 * operator object is used, it indicates that this element is intended to
 * be used as an operator, and is not simply some other parameter to be escaped.
 */
class Shell implements ShellOperatorInterface
{
    protected $value;

    public static function op($operator)
    {
        static::validateOp($operator);
        return new self($operator);
    }

    public static function preEscaped($value)
    {
        return new self($value);
    }

    public function __construct($value)
    {
        $this->value = $value;
    }

    public function __toString()
    {
        return $this->value;
    }

    protected static function validateOp($operator)
    {
        $valid = [
            '&&',
            '||',
            '|',
            '<',
            '>',
            '>>',
            ';',
        ];

        if (!in_array($operator, $valid)) {
            throw new \Exception($operator . ' is not a valid shell operator.');
        }
    }
}
