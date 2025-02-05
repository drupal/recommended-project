<?php

namespace Consolidation\SiteProcess;

use PHPUnit\Framework\TestCase;
use Consolidation\SiteProcess\Util\ArgumentProcessor;
use Consolidation\SiteAlias\SiteAlias;
use Consolidation\SiteProcess\Util\Escape;

class EscapeTest extends TestCase
{
    const DEFAULT_SITE_ALIAS = ['host' => 'example.com', ];
    const LINUX_SITE_ALIAS = ['host' => 'example.com', 'os' => 'Linux'];
    const WINDOWS_SITE_ALIAS = ['host' => 'example.com', 'os' => 'WIN'];

    /**
     * Data provider for testIsWindows.
     */
    public function isWindowsTestValues()
    {
        return [
            [
                'Linux',
                false,
                static::DEFAULT_SITE_ALIAS,
            ],

            [
                'Linux',
                false,
                static::LINUX_SITE_ALIAS,
            ],

            [
                'WIN',
                true,
                static::WINDOWS_SITE_ALIAS,
            ],
        ];
    }

    /**
     * Test the isWindows method.
     *
     * @dataProvider isWindowsTestValues
     */
    public function testIsWindows(
        $expected,
        $expectToBeWindows,
        $siteAliasData)
    {
        $siteAlias = new SiteAlias($siteAliasData, '@alias.dev');
        $actual = $siteAlias->os();
        $this->assertEquals($expected, $actual);
        $actuallyIsWindows = Escape::isWindows($siteAlias->os());
        $this->assertEquals($expectToBeWindows, $actuallyIsWindows);
    }

    /**
     * Data provider for testEscapeForSite.
     */
    public function escapeForSiteTestValues()
    {
        return [
            [
                'foo',
                'foo',
                static::DEFAULT_SITE_ALIAS,
            ],

            [
                'foo',
                'foo',
                static::LINUX_SITE_ALIAS,
            ],

            [
                'foo',
                'foo',
                static::WINDOWS_SITE_ALIAS,
            ],

            [
                "'foo bar'",
                'foo bar',
                static::DEFAULT_SITE_ALIAS,
            ],

            [
                "'foo bar'",
                'foo bar',
                static::LINUX_SITE_ALIAS,
            ],

            [
                '"foo bar"',
                'foo bar',
                static::WINDOWS_SITE_ALIAS,
            ],

            [
                "'don'\\''t forget'",
                "don't forget",
                static::DEFAULT_SITE_ALIAS,
            ],

            [
                "'don'\\''t forget'",
                "don't forget",
                static::LINUX_SITE_ALIAS,
            ],

            [
                '"don\'t forget"',
                "don't forget",
                static::WINDOWS_SITE_ALIAS,
            ],

            [
                "'I'\''ll try the \"easy\" fix.'",
                "I'll try the \"easy\" fix.",
                static::DEFAULT_SITE_ALIAS,
            ],

            [
                "'I'\''ll try the \"easy\" fix.'",
                "I'll try the \"easy\" fix.",
                static::LINUX_SITE_ALIAS,
            ],

            [
                '"I\'ll try the ""easy"" fix."',
                "I'll try the \"easy\" fix.",
                static::WINDOWS_SITE_ALIAS,
            ],

            [
                "'a b'",
                "a\tb",
                static::DEFAULT_SITE_ALIAS,
            ],

            [
                "'a b'",
                "a\tb",
                static::LINUX_SITE_ALIAS,
            ],

            [
                '"a b"',
                "a\tb",
                static::WINDOWS_SITE_ALIAS,
            ],

        ];
    }

    /**
     * Test the forSite method.
     *
     * @dataProvider escapeForSiteTestValues
     */
    public function testEscapeForSite(
        $expected,
        $arg,
        $siteAliasData)
    {
        $siteAlias = new SiteAlias($siteAliasData, '@alias.dev');

        $actual = Escape::forSite($siteAlias, $arg);
        $this->assertEquals($expected, $actual);
    }
}
