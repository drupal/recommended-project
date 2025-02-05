<?php
namespace Consolidation\Filter\Operators;

use Consolidation\Filter\OperatorInterface;
use Dflydev\DotAccessData\Data;

/**
 * Test for equality
 */
class NotOp implements OperatorInterface
{
    protected $op;

    public function __construct($op)
    {
        $this->op = $op;
    }

    /**
     * Test the provided value to see if it matches our criteria.
     *
     * @param mixed $value
     * @return bool
     */
    public function test(Data $row)
    {
        return !$this->op->test($row);
    }

    /**
     * Return a string representation of this operator
     */
    public function __toString()
    {
        return "!{$this->op}";
    }
}
