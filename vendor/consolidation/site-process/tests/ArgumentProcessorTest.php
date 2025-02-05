<?php

namespace Consolidation\SiteProcess;

use PHPUnit\Framework\TestCase;
use Consolidation\SiteProcess\Util\ArgumentProcessor;
use Consolidation\SiteAlias\SiteAlias;

class ArgumentProcessorTest extends TestCase
{
    /**
     * Data provider for testArgumentProcessor.
     */
    public function argumentProcessorTestValues()
    {
        return [
            [
                '["ls", "-al"]',
                [],
                ['ls', '-al'],
                [],
                [],
            ],

            [
                '["drush", "status", "-vvv", "--fields=root,uri"]',
                [],
                ['drush', 'status'],
                ['vvv' => TRUE, 'fields' => 'root,uri'],
                [],
            ],

            [
                '["drush", "rsync", "a", "b", "--", "--exclude=vendor"]',
                [],
                ['drush', 'rsync', 'a', 'b',],
                [],
                ['exclude' => 'vendor'],
            ],

            [
                '["drush", "rsync", "a", "b", "--", "--exclude=vendor", "--include=vendor/autoload.php"]',
                [],
                ['drush', 'rsync', 'a', 'b', '--', '--include=vendor/autoload.php'],
                [],
                ['exclude' => 'vendor'],
            ],
        ];
    }

    /**
     * Test the SiteProcess class.
     *
     * @dataProvider argumentProcessorTestValues
     */
    public function testArgumentProcessor(
        $expected,
        $siteAliasData,
        $args,
        $options,
        $optionsPassedAsArgs)
    {
        $siteAlias = new SiteAlias($siteAliasData, '@alias.dev');
        $processor = new ArgumentProcessor();

        $actual = $processor->selectArgs($siteAlias, $args, $options, $optionsPassedAsArgs);
        $actual = '["' . implode('", "', $actual) . '"]';
        $this->assertEquals($expected, $actual);
    }
}
