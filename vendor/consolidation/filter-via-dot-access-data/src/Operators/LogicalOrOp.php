<?php
namespace Consolidation\Filter\Operators;

use Consolidation\Filter\OperatorInterface;
use Dflydev\DotAccessData\Data;

/**
 * Test for equality
 */
class LogicalOrOp implements OperatorInterface
{
    protected $lhs;
    protected $rhs;

    public function __construct($lhs, $rhs)
    {
        $this->lhs = $lhs;
        $this->rhs = $rhs;
    }

    /**
     * Test the provided value to see if it matches our criteria.
     *
     * @param mixed $value
     * @return bool
     */
    public function test(Data $row)
    {
        return $this->lhs->test($row) || $this->rhs->test($row);
    }

    /**
     * Return a string representation of this operator
     */
    public function __toString()
    {
        return "{$this->lhs}||{$this->rhs}";
    }
}
