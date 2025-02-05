<?php

namespace Consolidation\Filter;

use PHPUnit\Framework\TestCase;
use Dflydev\DotAccessData\Data;

class FactoryTest extends TestCase
{
    protected $factory;

    public function setUp(): void
    {
        $this->factory = LogicalOpFactory::get();
    }

    /**
     * Data provider for testFactoryParsing.
     *
     * Return an array of arrays, each of which contains the parameter
     * values to be used in one invocation of the testExample test function.
     */
    public function factoryParsingTestValues()
    {
        return [
            ['a=b',],
            ['a*=b',],
            ['a~=#b#',],
            ['!a=b',],
            ['!a*=b',],
            ['!a~=#b#',],
            ['a!=b', '!a=b',],
            ['a!*=b', '!a*=b',],
            ['a!~=#b#', '!a~=#b#',],

            ['a=b&&c=d',],
            ['a*=b||c=d',],
            ['a~=#b#&&c~=d',],
            ['!a=b||!c=d',],
            ['!a*=b&&c*=d',],
            ['!a~=#b#&&c=d',],
            ['a!=b&&c!=d', '!a=b&&!c=d',],
            ['a!*=b||c!*=d', '!a*=b||!c*=d',],
            ['a!~=#b#&&c!~=#d#', '!a~=#b#&&!c~=#d#',],

            ['a=b&&c=d&&e=f',],
            ['a=b||c=d&&e=f',],
            ['a=b||c=d||e=f',],
            ['a=b&&c=d&&e=f&&g=h',],
            ['a=b||c=d||e=f||g=h',],
            ['a=b&&c=d||e=f&&g=h||i=j',],
            ['a=b&&c=d&&e=f||g=h||i=j',],
            ['a=b||c=d||e=f||g=h&&i=j',],

        ];
    }

    /**
     * Test our example class. Each time this function is called, it will
     * be passed data from the data provider function idendified by the
     * dataProvider annotation.
     *
     * @dataProvider factoryParsingTestValues
     */
    public function testFactoryParsing($expr, $expected = false)
    {
        // The expected value is often the source value
        if ($expected === false) {
            $expected = $expr;
        }
        $op = $this->factory->evaluate($expr);
        $this->assertEquals($expected, (string)$op);
    }

    /**
     * Data provider for testFactoryEvaluation.
     *
     * Return an array of arrays, each of which contains the parameter
     * values to be used in one invocation of the testExample test function.
     */
    public function factoryEvaluationTestValues()
    {
        return [
            ['a=b', ['a' => 'b'], true,],
            ['a=b', ['a' => 'abc'], false,],
            ['a=b', ['b' => 'b'], false,],

            ['a*=b', ['a' => 'b'], true,],
            ['a*=b', ['a' => 'abc'], true,],
            ['a*=b', ['b' => 'b'], false,],

            ['a~=#b#', ['a' => 'b'], true,],
            ['a~=#b#', ['a' => 'abc'], true,],
            ['a~=#b#', ['b' => 'b'], false,],

            ['a.b=c', ['a' => ['b' => 'c']], true,],
            ['a.b=c', ['a' => ['b' => 'abcd']], false,],
            ['a.b=c', ['b' => ['b' => 'c']], false,],

            ['a.b*=c', ['a' => ['b' => 'c']], true,],
            ['a.b*=c', ['a' => ['b' => 'abcd']], true,],
            ['a.b*=c', ['b' => ['b' => 'c']], false,],

            ['a.b~=#c#', ['a' => ['b' => 'c']], true,],
            ['a.b~=#c#', ['a' => ['b' => 'abcd']], true,],
            ['a.b~=#c#', ['b' => ['b' => 'c']], false,],

            ['a=b&&c=d', ['a' => 'b', 'c' => 'd'], true,],
            ['a=b&&c=d', ['a' => 'b', 'c' => 'xd'], false,],
            ['a=b||c=d', ['a' => 'b', 'c' => 'xd'], true,],

            ['a*=b&&c*=d', ['a' => 'b', 'c' => 'd'], true,],
            ['a*=b&&c*=d', ['a' => 'b', 'c' => 'xd'], true,],
            ['a*=b&&c*=d', ['a' => 'b', 'c' => 'xy'], false,],
            ['a*=b||c*=d', ['a' => 'b', 'c' => 'xd'], true,],
            ['a*=b||c*=d', ['a' => 'xb', 'c' => 'xd'], true,],
            ['a*=b||c*=d', ['a' => 'xy', 'c' => 'xy'], false,],

            ['a!=b', ['a' => 'b'], false,],
            ['a!=b', ['a' => 'abc'], true,],
            ['!a=b', ['b' => 'b'], true,],

        ];
    }

    /**
     * Test our example class. Each time this function is called, it will
     * be passed data from the data provider function idendified by the
     * dataProvider annotation.
     *
     * @dataProvider factoryEvaluationTestValues
     */
    public function testFactoryEvaluation($expr, $source, $expected)
    {
        $op = $this->factory->evaluate($expr);
        $data = new Data($source);
        $this->assertEquals($expected, $op->test($data));
    }
}
