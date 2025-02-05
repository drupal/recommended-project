<?php
namespace Consolidation\Filter;

use Dflydev\DotAccessData\Data;

use Consolidation\Filter\Operators\ContainsOp;
use Consolidation\Filter\Operators\EqualsOp;
use Consolidation\Filter\Operators\RegexOp;
use Consolidation\Filter\Operators\NotOp;

/**
 * Convert a simple operator expression into an Operator.
 *
 * The supported operators include:
 *
 *      key=value           Equals
 *      key*=value          Contains value
 *      key~=#regex#        Regular expression match
 *
 * It is also possible to negate the result of an operator by
 * adding a logical-not operator either before the entire expression,
 * e.g. !key=value, or before the operator, e.g. key!=value.
 *
 */
class OperatorFactory implements FactoryInterface
{
    public function __construct()
    {
    }

    /**
     * Create an operator or a set of operators from the expression.
     *
     * @param string $expression
     * @return OperatorInterface
     */
    public function evaluate($expression, $defaultField = false)
    {
        if ($expression[0] == '!') {
            $op = $this->evaluateNonNegated(substr($expression, 1), $defaultField);
            return new NotOp($op);
        }
        return $this->evaluateNonNegated($expression, $defaultField);
    }

    protected function evaluateNonNegated($expression, $defaultField = false)
    {
        list($key, $op, $comparitor) = $this->splitOnOperator($expression, $defaultField);
        if (empty($key) || empty($op)) {
            throw new \Exception('Could not parse expression ' . $expression);
        }

        if ($op[0] == '!') {
            $op = $this->instantiate($key, substr($op, 1), $comparitor);
            return new NotOp($op);
        }
        return $this->instantiate($key, $op, $comparitor);
    }

    protected function instantiate($key, $op, $comparitor)
    {
        switch ($op) {
            case '=':
                return new EqualsOp($key, $comparitor);
            case '*=':
                return new ContainsOp($key, $comparitor);
            case '~=':
                return new RegexOp($key, $comparitor);
        }

        throw new \Exception('Unrecognized operator ' . $op);
    }

    /**
     * Given an expression in the form 'key=comparitor', return a list
     * containing the key, the operator, and the comparitor. The operator
     * can be any of: =, *=, ~=, !=, !*= or !~=.
     *
     * @param string @expression
     * @return array
     */
    protected function splitOnOperator($expression, $defaultField = false)
    {
        // If there is a default field, then any expression that is missing
        // an operator will be interpreted as "default field contains value".
        if (preg_match('#^[a-zA-Z0-9 _.:-]+$#', $expression) && ($defaultField !== false)) {
            return [$defaultField, '*=', $expression];
        }
        if (!preg_match('#([^!~*=]*)(!?~?\*?=)(.*)#', $expression, $matches)) {
            return ['', '', ''];
        }

        array_shift($matches);
        return $matches;
    }
}
