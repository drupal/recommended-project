<?php
namespace Consolidation\Filter;

use Dflydev\DotAccessData\Data;

/**
 * Operators perform simple logic on provided values.
 *
 * Example operators:
 *
 *      key?                Has key
 *      op&op               Logical AND
 *      op|op               Logical OR
 */
interface OperatorInterface
{
    /**
     * Test the provided value to see if it matches our criteria.
     *
     * @param mixed $value
     * @return bool
     */
    public function test(Data $row);
}
