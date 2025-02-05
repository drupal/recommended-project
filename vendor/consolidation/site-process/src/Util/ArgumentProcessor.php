<?php
namespace Consolidation\SiteProcess\Util;

use Consolidation\SiteAlias\SiteAliasInterface;
use Symfony\Component\Process\Process;
use Consolidation\SiteProcess\Transport\TransportInterface;

/**
 * ArgumentProcessor takes a set of arguments and options from the caller
 * and processes them with the provided site alias to produce a final
 * executable command that will run either locally or on a remote system,
 * as applicable.
 */
class ArgumentProcessor
{
    private $short_options = ['vv', 'vvv'];

    public function getShortOptions(): array
    {
        return $this->short_options;
    }

    public function setShortOptions(array $short_options): void
    {
        $this->short_options = $short_options;
    }

    /**
     * selectArgs selects the appropriate set of arguments for the command
     * to be executed and orders them as needed.
     *
     * @param SiteAliasInterface $siteAlias Description of
     * @param array $args Command and arguments to execute (source)
     * @param array $options key / value pair of option and value in include
     *   in final arguments
     * @param array $optionsPassedAsArgs key / value pair of option and value
     *   to include in final arguments after the '--' argument.
     * @return array Command and arguments to execute
     */
    public function selectArgs(SiteAliasInterface $siteAlias, $args, $options = [], $optionsPassedAsArgs = [])
    {
        // Split args into three arrays separated by the `--`
        list($leadingArgs, $dashDash, $remaingingArgs) = $this->findArgSeparator($args);
        $convertedOptions = $this->convertOptions($options);
        $convertedOptionsPassedAsArgs = $this->convertOptions($optionsPassedAsArgs);

        // If the caller provided options that should be passed as args, then we
        // always need a `--`, whether or not one existed to begin with in $args
        if (!empty($convertedOptionsPassedAsArgs)) {
            $dashDash = ['--'];
        }

        // Combine our separated args in the correct order. $dashDash will
        // always be `['--']` if $optionsPassedAsArgs or $remaingingArgs are
        // not empty, and otherwise will usually be empty.
        return array_merge(
            $leadingArgs,
            $convertedOptions,
            $dashDash,
            $convertedOptionsPassedAsArgs,
            $remaingingArgs
        );
    }

    /**
     * findArgSeparator finds the "--" argument in the provided arguments list,
     * if present, and returns the arguments in three sets.
     *
     * @return array of three arrays, leading, "--" and trailing
     */
    protected function findArgSeparator($args)
    {
        $pos = array_search('--', $args);
        if ($pos === false) {
            return [$args, [], []];
        }

        return [
            array_slice($args, 0, $pos),
            ['--'],
            array_slice($args, $pos + 1),
        ];
    }

    /**
     * convertOptions takes an associative array of options (key / value) and
     * converts it to an array of strings in the form --key=value.
     *
     * @param array $options in key => value form
     * @return array options in --option=value form
     */
    protected function convertOptions($options)
    {
        $result = [];
        foreach ($options as $option => $value) {
            $dashes = str_repeat('-', $this->dashCount($option));
            if ($value === true || $value === null) {
                $result[] = $dashes . $option;
            } elseif ($value === false) {
                // Ignore this option.
            } else {
                $result[] = "{$dashes}{$option}={$value}";
            }
        }

        return $result;
    }

    protected function dashCount($name): int
    {
        return in_array($name, $this->getShortOptions()) ? 1 : 2;
    }
}
