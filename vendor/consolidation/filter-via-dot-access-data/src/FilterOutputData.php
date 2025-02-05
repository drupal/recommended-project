<?php
namespace Consolidation\Filter;

use Dflydev\DotAccessData\Data;

/**
 * Remove output result rows that do not match filter criteria.
 *
 * Only usable with nested arrays, e.g. as you might provide to RowsOfFields
 */
class FilterOutputData
{
    public function __construct()
    {
    }

    /**
     * Filter the provided data.
     */
    public function filter($data, OperatorInterface $op)
    {
        $result = [];

        foreach ($data as $id => $value) {
            $row = new Data($value);
            if (!isset($value['id'])) {
                $row->set('id', $id);
            }

            if ($op->test($row)) {
                $result[$id] = $value;
            }
        }

        return $result;
    }
}
