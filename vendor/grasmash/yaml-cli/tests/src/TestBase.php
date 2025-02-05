<?php

namespace Grasmash\YamlCli\Tests;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;

/**
 * Class BltTestBase.
 *
 * Base class for all tests that are executed for BLT itself.
 */
abstract class TestBase extends TestCase
{

    /** @var Application */
    protected $application;

    /** @var string */
    protected $temp_file = '';

    /**
     * {@inheritdoc}
     *
     * @see https://symfony.com/doc/current/console.html#testing-commands
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->application = new Application();
    }

    /**
     * Removes temporary file.
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        // This will only exist if a test called setupTemporaryConfigFiles().
        if ($this->temp_file && file_exists($this->temp_file)) {
            unlink($this->temp_file);
        }
    }


    /**
     * Creates a temporary copy of a file and assigns it to $this->temp_file.
     *
     * @param string $source
     *   The filename of the source file.
     * @param string $destination
     *   The filename of the destination file.
     *
     * @return bool
     *   TRUE if the file was created. Otherwise, FALSE.
     */
    protected function createTemporaryFile($source, $destination)
    {
        $source_path = realpath($source);
        if (file_exists($source_path)) {
            copy($source_path, $destination);
            $destination_path = realpath($destination);
            $this->temp_file = $destination_path;

            return true;
        }

        return false;
    }

    /**
     * Creates a temporary copy of config files so that they can be modified.
     */
    protected function setupTemporaryConfigFiles()
    {
        // Make a temporary copy of good.yml so that we can update a value
        // without destroying the original.
        $this->createTemporaryFile(__DIR__ . '/../resources/good.yml', __DIR__ . '/../resources/temp.yml');
    }
}
