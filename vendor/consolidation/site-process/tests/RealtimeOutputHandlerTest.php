<?php

namespace Consolidation\SiteProcess;

use Consolidation\SiteProcess\Util\Escape;
use PHPUnit\Framework\TestCase;
use Consolidation\SiteProcess\Util\ArgumentProcessor;
use Consolidation\SiteAlias\SiteAlias;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Input\ArrayInput;

class RealtimeOutputHandlerTest extends TestCase
{
    /**
     * Data provider for testRealtimeOutputHandler.
     */
    public function realtimeOutputHandlerTestValues()
    {
        return [
            [
                'hello, world',
                '',
                ['echo', 'hello, world'],
                'LINUX',
            ],

            [
                '"hello, world"',
                '',
                ['echo', 'hello, world'],
                'WIN'
            ],

            [
                'README.md',
                '',
                ['ls', 'README.md'],
                'LINUX',
            ],

            [
                '',
                'No such file or directory',
                ['ls', 'no/such/file'],
                'LINUX',
            ],
        ];
    }

    /**
     * Test the RealtimeOutputHandler class.
     *
     * @dataProvider realtimeOutputHandlerTestValues
     */
    public function testRealtimeOutputHandler($expectedStdout, $expectedStderr, $args, $os)
    {
        if (Escape::isWindows() != Escape::isWindows($os)) {
          $this->markTestSkipped("OS isn't supported");
        }
        $stdin = new ArrayInput([]);
        $stdout = new BufferedOutput();
        $stderr = new BufferedOutput();
        $symfonyStyle = new SymfonyStyle($stdin, $stdout);

        $process = new ProcessBase($args);
        $process->setRealtimeOutput($symfonyStyle, $stderr);
        $process->run($process->showRealtime());

        $this->assertEquals($expectedStdout, trim($stdout->fetch()));
        if (empty($expectedStderr)) {
            $this->assertEquals('', trim($stderr->fetch()));
        }
        else {
            $this->assertStringContainsString($expectedStderr, trim($stderr->fetch()));
        }
    }
}
