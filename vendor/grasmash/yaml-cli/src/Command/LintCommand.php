<?php

namespace Grasmash\YamlCli\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class CreateProjectCommand
 *
 * @package Grasmash\YamlCli\Command
 */
class LintCommand extends CommandBase
{

    /**
     * {inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('lint')
            ->setDescription('Validates that a given YAML file has valid syntax.')
            ->addUsage("path/to/file.yml")
            ->addArgument(
                'filename',
                InputArgument::REQUIRED,
                "The filename of the YAML file"
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
        $yaml_parsed = $this->loadYamlFile($filename);
        if (!$yaml_parsed) {
            // Exit with a status of 1.
            return 1;
        }

        if (OutputInterface::VERBOSITY_VERBOSE === $output->getVerbosity()) {
            $output->writeln("<info>The file $filename contains valid YAML.</info>");
        }

        return 0;
    }
}
