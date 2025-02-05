<?php

namespace Consolidation\Filter;

use PHPUnit\Framework\TestCase;
use Dflydev\DotAccessData\Data;

class OpTest extends TestCase
{
    /**
     * Data provider for testOpsParsing.
     *
     * Return an array of arrays, each of which contains the parameter
     * values to be used in one invocation of the testExample test function.
     */
    public function opsParsingTestValues()
    {
        return [
            [\Consolidation\Filter\Operators\EqualsOp::class, 'a', 'b', 'a=b',],
            [\Consolidation\Filter\Operators\ContainsOp::class, 'a', 'b', 'a*=b',],
            [\Consolidation\Filter\Operators\RegexOp::class, 'a', '#b#', 'a~=#b#',],
        ];
    }

    /**
     * Test our example class. Each time this function is called, it will
     * be passed data from the data provider function idendified by the
     * dataProvider annotation.
     *
     * @dataProvider opsParsingTestValues
     */
    public function testOpsParsing($opName, $key, $comparitor, $expected)
    {
        $op = new $opName($key, $comparitor);
        $this->assertEquals($expected, (string)$op);
    }

    /**
     * Data provider for testOpsEvaluation.
     *
     * Return an array of arrays, each of which contains the parameter
     * values to be used in one invocation of the testExample test function.
     */
    public function opsEvaluationTestValues()
    {
        return [
            [\Consolidation\Filter\Operators\EqualsOp::class, 'a', 'b', ['a' => 'b'], true,],
            [\Consolidation\Filter\Operators\EqualsOp::class, 'a', 'b', ['a' => 'abc'], false,],
            [\Consolidation\Filter\Operators\EqualsOp::class, 'a', 'b', ['b' => 'b'], false,],

            [\Consolidation\Filter\Operators\ContainsOp::class, 'a', 'b', ['a' => 'b'], true,],
            [\Consolidation\Filter\Operators\ContainsOp::class, 'a', 'b', ['a' => 'abc'], true,],
            [\Consolidation\Filter\Operators\ContainsOp::class, 'a', 'b', ['b' => 'b'], false,],

            [\Consolidation\Filter\Operators\RegexOp::class, 'a', '#b#', ['a' => 'b'], true,],
            [\Consolidation\Filter\Operators\RegexOp::class, 'a', '#b#', ['a' => 'abc'], true,],
            [\Consolidation\Filter\Operators\RegexOp::class, 'a', '#b#', ['b' => 'b'], false,],

            [\Consolidation\Filter\Operators\EqualsOp::class, 'a.b', 'c', ['a' => ['b' => 'c']], true,],
            [\Consolidation\Filter\Operators\EqualsOp::class, 'a.b', 'c', ['a' => ['b' => 'abcd']], false,],
            [\Consolidation\Filter\Operators\EqualsOp::class, 'a.b', 'c', ['b' => ['b' => 'c']], false,],

            [\Consolidation\Filter\Operators\ContainsOp::class, 'a.b', 'c', ['a' => ['b' => 'c']], true,],
            [\Consolidation\Filter\Operators\ContainsOp::class, 'a.b', 'c', ['a' => ['b' => 'abcd']], true,],
            [\Consolidation\Filter\Operators\ContainsOp::class, 'a.b', 'c', ['b' => ['b' => 'c']], false,],

            [\Consolidation\Filter\Operators\RegexOp::class, 'a.b', '#c#', ['a' => ['b' => 'c']], true,],
            [\Consolidation\Filter\Operators\RegexOp::class, 'a.b', '#c#', ['a' => ['b' => 'abcd']], true,],
            [\Consolidation\Filter\Operators\RegexOp::class, 'a.b', '#c#', ['b' => ['b' => 'c']], false,],

        ];
    }

    /**
     * Test our example class. Each time this function is called, it will
     * be passed data from the data provider function idendified by the
     * dataProvider annotation.
     *
     * @dataProvider opsEvaluationTestValues
     */
    public function testOpsEvaluation($opName, $key, $comparitor, $source, $expected)
    {
        $op = new $opName($key, $comparitor);
        $data = new Data($source);
        $this->assertEquals($expected, $op->test($data));
    }
}
