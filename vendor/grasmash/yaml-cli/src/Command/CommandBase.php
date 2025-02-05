<?php

namespace Grasmash\YamlCli\Command;

use Dflydev\DotAccessData\Data;
use Grasmash\YamlCli\Loader\JsonFileLoader;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\FormatterHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Yaml\Yaml;

/**
 * Class CommandBase
 *
 * @package Grasmash\YamlCli\Command
 */
abstract class CommandBase extends Command
{

    /** @var Filesystem */
    protected $fs;

    /**
     * @var InputInterface
     */
    protected $input;

    /**
     * @var OutputInterface
     */
    protected $output;

    /** @var FormatterHelper */
    protected $formatter;

    /**
     * Initializes the command just after the input has been validated.
     *
     * @param InputInterface $input An InputInterface instance
     * @param OutputInterface $output An OutputInterface instance
     */
    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        $this->input = $input;
        $this->output = $output;
        $this->formatter = $this->getHelper('formatter');
        $this->fs = new Filesystem();
    }

    /**
     * Loads a yaml file.
     *
     * @param $filename
     *   The file name.
     *
     * @return array|bool
     *   The parsed content of the yaml file. FALSE if an error occured.
     */
    public function loadYamlFile($filename)
    {
        if (!file_exists($filename)) {
            $this->output->writeln("<error>The file $filename does not exist.</error>");

            return false;
        }

        try {
            $contents = Yaml::parse(file_get_contents($filename));
        } catch (\Exception $e) {
            $this->output->writeln("<error>There was an error parsing $filename. The contents are not valid YAML.</error>");
            $this->output->writeln($e->getMessage());

            return false;
        }

        return $contents;
    }

    /**
     * Writes YAML data to a file.
     *
     * @param string $filename
     *   The filename.
     * @param Data $data
     *   The YAML file contents.
     *
     * @return bool
     *   TRUE if file was written successfully. Otherwise, FALSE.
     */
    public function writeYamlFile($filename, $data)
    {
        try {
            // @todo Allow the inline and indent variables to be set via command line option.
            $yaml = Yaml::dump($data->export(), 3, 2);
        } catch (\Exception $e) {
            $this->output->writeln("<error>There was an error dumping the YAML contents for $filename.</error>");
            $this->output->writeln($e->getMessage());

            return false;
        }

        try {
            // @todo Use Symfony file system instead so that exceptions can be caught.
            file_put_contents($filename, $yaml);
        } catch (\Exception $e) {
            $this->output->writeln("<error>There was an writing to $filename.</error>");
            $this->output->writeln($e->getMessage());

            return false;
        }

        return true;
    }

    /**
     * Checks if a key exists in an array.
     *
     * Supports dot notation for keys. E.g., first.second.parts.
     *
     * @param array $data
     *   The array of data that may contain key.
     * @param string $key
     *   The array key, optionally in dot notation format.
     *
     * @return bool
     *
     */
    protected function checkKeyExists($data, $key)
    {
        if (!$data->has($key)) {
            $this->output->writeln("<error>The key $key does not exist.");

            return false;
        }

        return true;
    }
}
