<?php

namespace Grasmash\YamlCli\Tests\Command;

use Dflydev\DotAccessData\Data;
use Grasmash\YamlCli\Command\UpdateValueCommand;
use Grasmash\YamlCli\Tests\TestBase;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;

class UpdateValueCommandTest extends TestBase
{

    /** @var string */
    protected $temp_file;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->setupTemporaryConfigFiles();
    }

    /**
     * Tests the 'update:value' command.
     *
     * @dataProvider getValueProvider
     */
    public function testUpdateValue($file, $key, $value, $type, $expected_value, $expected_output, $expected_exit_code)
    {
        $commandTester = $this->runCommand($file, $key, $value, $type);
        $output = $commandTester->getDisplay();
        $this->assertStringContainsString($expected_output, $output);

        $contents = $this->getCommand()->loadYamlFile($file);
        $data = new Data($contents);
        $this->assertEquals($expected_value, $data->get($key));
        $this->assertEquals($expected_exit_code, $commandTester->getStatusCode());
    }

    /**
     * Tests that passing a missing file outputs expected error.
     */
    public function testMissingFile()
    {
        $commandTester = $this->runCommand('missing.yml', 'not-real', 'still-not-real', null);
        $this->assertStringContainsString("The file missing.yml does not exist.", $commandTester->getDisplay());
    }

    /**
     * Gets the update:value command.
     *
     * @return \Symfony\Component\Console\Command\Command
     */
    protected function getCommand(): Command
    {
        $this->application->add(new UpdateValueCommand());
        return $this->application->find('update:value');
    }

    /**
     * Runs the update:value commnd.
     *
     * @param string $file
     *   The filename.
     * @param string $key
     *   The key for which to update the value.
     * @param string $value
     *   The new value.
     * @param string $type
     *
     * @return \Symfony\Component\Console\Tester\CommandTester
     */
    protected function runCommand(string $file, string $key, string $value, $type): CommandTester
    {
        $command = $this->getCommand();
        $commandTester = new CommandTester($command);
        $params = [
            'command' => $command->getName(),
            'filename' => $file,
            'key' => $key,
            'value' => $value,
        ];
        if ($type) {
            $params['--type'] = $type;
        }
        $commandTester->execute($params);

        return $commandTester;
    }

    /**
     * Provides values to testUpdateValue().
     *
     * @return array
     *   An array of values to test.
     */
    public function getValueProvider(): array
    {
        $file = 'tests/resources/temp.yml';

        return [
            [
                $file,
                'deep-array.second.third.fourth',
                'goodbye world',
                null,
                'goodbye world',
                "The value for key 'deep-array.second.third.fourth' was set to 'goodbye world' (string) in $file.",
                0,
            ],
            [
                $file,
                'flat-array.0',
                'goodbye world',
                null,
                'goodbye world',
                "The value for key 'flat-array.0' was set to 'goodbye world' (string) in $file.",
                0,
            ],
            [
                $file,
                'inline-array.0',
                'goodbye world',
                null,
                'goodbye world',
                "The value for key 'inline-array.0' was set to 'goodbye world' (string) in $file.",
                0,
            ],
            [
                $file,
                'new-key.sub-key',
                'hello world',
                null,
                'hello world',
                "The value for key 'new-key.sub-key' was set to 'hello world' (string) in $file.",
                0,
            ],
            [
                $file,
                'integer.0',
                '0',
                'int',
                0,
                "The value for key 'integer.0' was set to '0' (integer) in $file.",
                0,
            ],
            [
                $file,
                'integer.1',
                '1',
                'integer',
                1,
                "The value for key 'integer.1' was set to '1' (integer) in $file.",
                0,
            ],
            [
                $file,
                'boolean.0',
                'false',
                null,
                false,
                "The value for key 'boolean.0' was set to 'false' (boolean) in $file.",
                0,
            ],
            [
                $file,
                'boolean.1',
                'true',
                null,
                true,
                "The value for key 'boolean.1' was set to 'true' (boolean) in $file.",
                0,
            ],
            [
                $file,
                'boolean.0',
                '0',
                'bool',
                false,
                "The value for key 'boolean.0' was set to '0' (boolean) in $file.",
                0,
            ],
            [
                $file,
                'boolean.1',
                '1',
                'boolean',
                true,
                "The value for key 'boolean.1' was set to '1' (boolean) in $file.",
                0,
            ],
            [
                $file,
                'string.0',
                'false',
                'string',
                'false',
                "The value for key 'string.0' was set to 'false' (string) in $file.",
                0,
            ],
            [
                $file,
                'string.1',
                'true',
                'string',
                'true',
                "The value for key 'string.1' was set to 'true' (string) in $file.",
                0,
            ],
            [
                $file,
                'string.2',
                'null',
                'string',
                'null',
                "The value for key 'string.2' was set to 'null' (string) in $file.",
                0,
            ],
            [
                $file,
                'null.0',
                'null',
                null,
                null,
                "The value for key 'null.0' was set to 'null' (NULL) in $file.",
                0,
            ],
            [
                $file,
                'null.0',
                '~',
                'null',
                null,
                "The value for key 'null.0' was set to '~' (NULL) in $file.",
                0,
            ],
            [
                $file,
                'float.0',
                '1.0',
                'float',
                1.0,
                "The value for key 'float.0' was set to '1.0' (double) in $file.",
                0,
            ],
            [
                $file,
                'float.1',
                '1.0',
                'double',
                1.0,
                "The value for key 'float.1' was set to '1.0' (double) in $file.",
                0,
            ],
            [
                $file,
                'float.2',
                '1.0',
                'real',
                1.0,
                "The value for key 'float.2' was set to '1.0' (double) in $file.",
                0,
            ],
        ];
    }
}
