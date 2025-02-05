<?php

namespace Consolidation\Filter\Hooks;

use Consolidation\AnnotatedCommand\CommandData;
use Consolidation\Filter\LogicalOpFactory;
use Consolidation\Filter\FilterOutputData;
use Symfony\Component\Yaml\Yaml;

class FilterHooks
{
    /**
     * @hook alter @filter-output
     * @option $filter Filter output based on provided expression
     * @default $filter ''
     */
    public function filterOutput($result, CommandData $commandData)
    {
        $expr = $commandData->input()->getOption('filter');
        $default_field = $commandData->annotationData()->get('filter-default-field');
        if (!empty($expr)) {
            $factory = LogicalOpFactory::get();
            $op = $factory->evaluate($expr, $default_field);
            $filter = new FilterOutputData();
            $result = $this->wrapFilteredResult($filter->filter($result, $op), $result);
        }

        return $result;
    }

    /**
     * If the source data was wrapped in a marker class such
     * as RowsOfFields, then re-apply the wrapper.
     */
    protected function wrapFilteredResult($data, $source)
    {
        if (!$source instanceof \ArrayObject) {
            return $data;
        }
        $sourceClass = get_class($source);

        return new $sourceClass($data);
    }
}
