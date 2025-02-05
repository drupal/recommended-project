<?php

namespace Grasmash\YamlCli\Tests\Command;

use Dflydev\DotAccessData\Data;
use Grasmash\YamlCli\Command\UpdateKeyCommand;
use Grasmash\YamlCli\Tests\TestBase;
use Symfony\Component\Console\Tester\CommandTester;

class UpdateKeyCommandTest extends TestBase
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
     * Tests the 'update:key' command.
     *
     * @dataProvider getValueProvider
     */
    public function testUpdateKey($file, $key, $new_key, $expected_output, $expected_exit_code)
    {
        $contents = $this->getCommand()->loadYamlFile($file);
        $data = new Data($contents);
        $value = $data->get($key, null);

        $commandTester = $this->runCommand($file, $key, $new_key);
        $output = $commandTester->getDisplay();
        $this->assertStringContainsString($expected_output, $output);
        $this->assertEquals($expected_exit_code, $commandTester->getStatusCode());

        // If we expected the command to be successful, test the file contents.
        if (!$expected_exit_code) {
            $contents = $this->getCommand()->loadYamlFile($file);
            $data = new Data($contents);
            $this->assertTrue($data->has($new_key), "The file $file does not contain the new key $new_key. It should.");
            $this->assertNotTrue($data->has($key), "The file $file contains the old key $key. It should not.");
            $this->assertEquals(
                $value,
                $data->get($new_key),
                "The value of key $new_key does not equal the value of the original key $key"
            );
        }
    }

    /**
     * Tests that passing a missing file outputs expected error.
     */
    public function testMissingFile()
    {
        $commandTester = $this->runCommand('missing.yml', 'not-real', 'still-not-real');
        $this->assertStringContainsString("The file missing.yml does not exist.", $commandTester->getDisplay());
    }

    /**
     * Gets the update:key command.
     *
     * @return UpdateKeyCommand
     */
    protected function getCommand()
    {
        $this->application->add(new UpdateKeyCommand());
        $command = $this->application->find('update:key');

        return $command;
    }

    /**
     * Runs the update:key command.
     *
     * @param string $file
     *   The filename.
     * @param string $key
     *   The original key.
     * @param string $new_key
     *   The new key.
     *
     * @return \Symfony\Component\Console\Tester\CommandTester
     */
    protected function runCommand($file, $key, $new_key)
    {
        $command = $this->getCommand();
        $commandTester = new CommandTester($command);
        $commandTester->execute([
            'command' => $command->getName(),
            'filename' => $file,
            'key' => $key,
            'new-key' => $new_key,
        ]);

        return $commandTester;
    }

    /**
     * Provides values to testUpdateKey().
     *
     * @return array
     *   An array of values to test.
     */
    public function getValueProvider()
    {
        $file = 'tests/resources/temp.yml';

        return [
            [
                $file,
                'deep-array.second.third.fourth',
                'deep-array.second.third.fifth',
                "The key 'deep-array.second.third.fourth' was changed to 'deep-array.second.third.fifth' in $file.",
                0,
            ],
            [
                $file,
                'flat-array.0',
                'flat-array.10',
                "The key 'flat-array.0' was changed to 'flat-array.10' in $file.",
                0,
            ],
            [
                $file,
                'inline-array.0',
                'inline-array.10',
                "The key 'inline-array.0' was changed to 'inline-array.10' in $file.",
                0,
            ],
            [
                $file,
                'fake-key',
                'new-key',
                "The key 'fake-key' does not exist in $file.",
                1,
            ],
        ];
    }
}
