<?php
namespace Consolidation\Filter;

use Dflydev\DotAccessData\Data;

use Consolidation\Filter\Operators\LogicalAndOp;
use Consolidation\Filter\Operators\LogicalOrOp;

/**
 * Convert an expression with logical operators into an Operator.
 */
interface FactoryInterface
{
    /**
     * Create an operator from an expression.
     *
     * @param string $expression
     * @return OperatorInterface
     */
    public function evaluate($expression, $defaultField = false);
}
