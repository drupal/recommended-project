<?php
namespace Consolidation\Filter;

use Dflydev\DotAccessData\Data;

use Consolidation\Filter\Operators\LogicalAndOp;
use Consolidation\Filter\Operators\LogicalOrOp;

/**
 * Convert an expression with logical operators into an Operator.
 */
class LogicalOpFactory implements FactoryInterface
{
    protected $factory;

    /**
     * Factory constructor
     * @param FactoryInterface|null $factory
     * @return FactoryInterface
     */
    public function __construct($factory = null)
    {
        $this->factory = $factory ?: new OperatorFactory();
    }

    /**
     * Factory factory
     * @return FactoryInterface
     */
    public static function get()
    {
        return new self();
    }

    /**
     * Create an operator or a set of operators from the expression.
     *
     * @param string $expression
     * @return OperatorInterface
     */
    public function evaluate($expression, $default_field = false)
    {
        $exprSet = $this->splitByLogicOp($expression);
        $result = $logicOp = false;

        foreach ($exprSet as $exprWithLogicOp) {
            $expr = $exprWithLogicOp[2];
            $rhs = $this->factory->evaluate($expr, $default_field);
            $result = $this->combineUsingLogicalOp($result, $logicOp, $rhs);
            // Logical operator between expressions is always one behind.
            $logicOp = $exprWithLogicOp[1];
        }

        return $result;
    }

    /**
     * Given an expression in a form similar to 'a=b&c=d|x=y',
     * produce a result as :
     *
     * [
     *   [
     *     0 => 'a=b',
     *     1 => '',
     *     2 => 'a=b',
     *   ],
     *   [
     *     0 => '&&c=d',
     *     1 => '&&',
     *     2 => 'c=d',
     *   ],
     *   [
     *     0 => '||x=y',
     *     1 => '||',
     *     2 => 'x=y',
     *   ],
     * ]
     *
     * This is the data structure returned by the former preg_match_all call
     * used, which was:
     *
     * preg_match_all('#([&|]*)([^&|]+)#', $expression, $exprSet, PREG_SET_ORDER)
     *
     * The new algorithm splices the expressions together manually, as it was
     * difficult to get preg_match_all to match && and || reliably.
     *
     * @param string $expression
     * @return array
     */
    protected function splitByLogicOp($expression)
    {
        if (!preg_match_all('#(&&|\|\|)#', $expression, $matches, PREG_OFFSET_CAPTURE)) {
            return [ [$expression, '', $expression] ];
        }
        $exprSet = [];
        $i = $offset = 0;
        foreach ($matches[0] as $opWithOffset) {
            list($op, $offset) = $opWithOffset;
            $expr = substr($expression, $i, $offset - $i);
            $i = $offset + strlen($op);
            $exprSet[] = [ "$op$expr", $op, $expr, ];
        }
        $expr = substr($expression, $offset + strlen($op));
        $exprSet[] = [ "$op$expr", $op, $expr];
        return $exprSet;
    }

    /**
     * Given the left-hand-side operator, a logical operator, and a
     * string expression, create the right-hand-side operator and combine
     * it with the provided lhs operator.
     *
     * @param Operator|false $lhs Left-hand-side operator
     * @param string $logicOp '&' or '|'
     * @param OperatorInterface $rhs Right-hand-side operator
     * @return Operator
     */
    protected function combineUsingLogicalOp($lhs, $logicOp, OperatorInterface $rhs)
    {
        // If this is the first term, just return the $rhs.
        // At this point, $logicOp is always empty.
        if (!$lhs || empty($logicOp)) {
            return $rhs;
        }

        // At this point, $logicOp is never empty.
        return $this->createLogicalOp($lhs, $logicOp, $rhs);
    }

    /**
     * Given the left-hand-side operator, a logical operator, and a
     * string expression, create the right-hand-side operator and combine
     * it with the provided lhs operator.
     *
     * @param Operator|false $lhs Left-hand-side operator
     * @param string $logicOp '&' or '|'
     * @param OperatorInterface $rhs Right-hand-side operator
     * @return Operator
     */
    protected function createLogicalOp(OperatorInterface $lhs, $logicOp, OperatorInterface $rhs)
    {
        switch ($logicOp) {
            case '&&':
                return new LogicalAndOp($lhs, $rhs);
            case '||':
                return new LogicalOrOp($lhs, $rhs);
        }
        throw new \Exception('Impossible logicOp received: ' . $logicOp);
    }
}
