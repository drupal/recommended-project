<?php

namespace Grasmash\YamlCli\Tests\Command;

use Grasmash\YamlCli\Command\GetValueCommand;
use Grasmash\YamlCli\Tests\TestBase;
use Symfony\Component\Console\Tester\CommandTester;

class GetValueCommandTest extends TestBase
{

    /**
     * Tests the 'get:value' command.
     *
     * @dataProvider getValueProvider
     */
    public function testGetValue($file, $key, $expected_output, $expected_exit_code)
    {
        $this->application->add(new GetValueCommand());

        $command = $this->application->find('get:value');
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
            [$file, 'deep-array.second.third.fourth', 'hello world', 0],
            [
                $file,
                'flat-array',
                '- one
- two
- three',
                0,
            ],
            [
                $file,
                'inline-array',
                '- one
- two
- three',
                0,
            ],
        ];
    }
}
