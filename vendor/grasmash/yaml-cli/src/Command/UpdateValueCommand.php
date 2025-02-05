<?php

namespace Grasmash\YamlCli\Command;

use Dflydev\DotAccessData\Data;
use Symfony\Component\Console\Exception\RuntimeException;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class CreateProjectCommand
 *
 * @package Grasmash\YamlCli\Command
 */
class UpdateValueCommand extends CommandBase
{

    /**
     * {inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('update:value')
            ->setDescription('Update the value for a specific key in a YAML file.')
            ->addUsage("path/to/file.yml example.key 'new value for example.key'")
            ->addArgument(
                'filename',
                InputArgument::REQUIRED,
                "The filename of the YAML file"
            )
            ->addArgument(
                'key',
                InputArgument::REQUIRED,
                "The key for the value to set, in dot notation"
            )
            ->addArgument(
                'value',
                InputArgument::REQUIRED,
                "The new value"
            )
            ->addOption('type', 't', InputOption::VALUE_REQUIRED, 'Set the variable type for the value. Accepted types are int, integer, bool, boolean, str, and string.');
    }

    /**
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     *
     * @return int 0 if everything went fine, or an exit code
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $filename = $input->getArgument('filename');
        $key = $input->getArgument('key');
        $raw_value = $input->getArgument('value');
        $yaml_parsed = $this->loadYamlFile($filename);
        if ($yaml_parsed === false) {
            // Exit with a status of 1.
            return 1;
        }

        $data = new Data($yaml_parsed);

        $value = $raw_value;
        if ($type = $input->getOption('type')) {
            $value = match ($type) {
                'int', 'integer' => (int) $raw_value,
                'bool', 'boolean' => (bool) $raw_value,
                'float', 'double', 'real' => (float) $raw_value,
                'str', 'string' => (string) $raw_value,
                'null' => null,
                default => throw new RuntimeException('The option type must have a value of int, integer, bool, or boolean.'),
            };
        } elseif (strtolower($value) === 'false') {
            $value = false;
        } elseif (strtolower($value) === 'true') {
            $value = true;
        } elseif (strtolower($value) === 'null') {
            $value = null;
        }


        $data->set($key, $value);

        if ($this->writeYamlFile($filename, $data)) {
            $this->output->writeln("<info>The value for key '$key' was set to '$raw_value' (" . gettype($value) . ") in $filename.</info>");
            return 0;
        }

        return 1;
    }
}
