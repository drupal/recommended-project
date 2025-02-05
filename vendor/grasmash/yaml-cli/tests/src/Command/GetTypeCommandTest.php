<?php

namespace Grasmash\YamlCli\Tests\Command;

use Grasmash\YamlCli\Command\GetTypeCommand;
use Grasmash\YamlCli\Tests\TestBase;
use Symfony\Component\Console\Tester\CommandTester;

class GetTypeCommandTest extends TestBase
{

    /**
     * Tests the 'get:value' command.
     *
     * @dataProvider getValueProvider
     */
    public function testGetValue($file, $key, $expected_output, $expected_exit_code)
    {
        $this->application->add(new GetTypeCommand());

        $command = $this->application->find('get:type');
        $commandTester = new CommandTester($command);
        $commandTester->execute([
            'command' => $command->getName(),
            'filename' => $file,
            'key' => $key,
        ]);

        $output = $commandTester->getDisplay();
        $this->assertStringContainsString($expected_output, $output);
        $this->assertEquals($expected_exit_code, $commandTester->getStatusCode());
    }

    /**
     * Provides values to testGetValue().
     *
     * @return array
     *   An array of values to test.
     */
    public function getValueProvider()
    {

        $file = 'tests/resources/good.yml';

        return [
            [$file, 'not-real', "The key not-real does not exist.", 1],
            [
                'missing.yml',
                'not-real',
                "The file missing.yml does not exist.",
                1,
            ],
            [$file, 'deep-array.second.third.fourth', 'string', 0],
            [$file, 'flat-array', 'array', 0],
            [$file, 'inline-array', 'array', 0],
            [$file, 'null-value', 'NULL', 0],
            [$file, 'bool-value', 'boolean', 0],
            [$file, 'int-value', 'integer', 0],
            [$file, 'float-value', 'double', 0],
        ];
    }
}
