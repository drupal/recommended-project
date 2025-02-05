<?php

namespace Grasmash\YamlCli\Command;

use Dflydev\DotAccessData\Data;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class GetTypeCommand
 *
 * @package Grasmash\YamlCli\Command
 */
class GetTypeCommand extends CommandBase
{

    /**
     * {inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('get:type')
            ->setDescription('Get the type of a value for a specific key in a YAML file.')
            ->addUsage("path/to/file.yml example.key")
            ->addArgument(
                'filename',
                InputArgument::REQUIRED,
                "The filename of the YAML file"
            )
            ->addArgument(
                'key',
                InputArgument::REQUIRED,
                "The key for the value to get the type of, in dot notation."
            );
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
        $yaml_parsed = $this->loadYamlFile($filename);
        if (!$yaml_parsed) {
            // Exit with a status of 1.
            return 1;
        }

        $data = new Data($yaml_parsed);
        if (!$this->checkKeyExists($data, $key)) {
            return 1;
        }

        $value = $data->get($key);
        $output->writeln(trim(gettype($value)));
        return 0;
    }
}
