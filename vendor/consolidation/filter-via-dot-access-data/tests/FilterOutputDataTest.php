<?php

namespace Consolidation\Filter;

use PHPUnit\Framework\TestCase;
use Dflydev\DotAccessData\Data;

class FilterOutputDataTest extends TestCase
{
    protected $factory;
    protected $filter;

    public function setUp(): void
    {
        $this->factory = LogicalOpFactory::get();
        $this->filter = new FilterOutputData();
    }

    /**
     * Data provider for testFilterData.
     *
     * Return an array of arrays, each of which contains the parameter
     * values to be used in one invocation of the testExample test function.
     */
    public function filterDataTestValues()
    {
        $source = [
            'a' => ['color' => 'red', 'shape' => 'round', 'id' => 'a'],
            'b' => ['color' => 'blue', 'shape' => 'square', 'id' => 'b'],
            'c' => ['color' => 'green', 'shape' => 'triangular', 'id' => 'c'],
        ];

        return [
            [$source, 'color=red', 'a', ],
            [$source, 'color=blue||shape=triangular', 'b,c', ],
            [$source, 'color=red&&shape=square', '', ],
            [$source, 'color=red||color=blue||color=green', 'a,b,c', ],
            [$source, 'color*=e&&shape=round&&shape!=square', 'a', ],
            [$source, 'color=red&&shape=round&&color=blue', '', ],
            [$source, 'id=c&&shape=triangular', 'c', ]
        ];
    }

    /**
     * Test our example class. Each time this function is called, it will
     * be passed data from the data provider function idendified by the
     * dataProvider annotation.
     *
     * @dataProvider filterDataTestValues
     */
    public function testFilterData($source, $expr, $expected)
    {
        $op = $this->factory->evaluate($expr);
        $actual = $this->filter->filter($source, $op);
        $this->assertEquals($expected, implode(',', array_keys($actual)));
    }
}
