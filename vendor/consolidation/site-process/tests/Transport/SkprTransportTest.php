<?php

namespace Consolidation\SiteProcess;

use Consolidation\SiteProcess\Transport\SkprTransport;
use PHPUnit\Framework\TestCase;
use Consolidation\SiteAlias\SiteAlias;

class SkprTransportTest extends TestCase
{
    /**
     * Data provider for testWrap.
     */
    public function wrapTestValues()
    {
        return [
            // Everything explicit.
            [
                'skpr exec dev -- ls',
                ['ls'],
                [
                    'skpr' => [
                        'env' => 'dev',
                    ]
                ],
            ],

            // Ensure we aren't escaping arguments after "--"
            [
                'skpr exec dev -- monday "tuesday" \'wednesday\'',
                ['monday', '"tuesday"', "'wednesday'"],
                [
                    'skpr' => [
                        'env' => 'dev'
                    ]
                ],
            ],
        ];
    }

    /**
     * @dataProvider wrapTestValues
     */
    public function testWrap($expected, $args, $siteAliasData)
    {
        $siteAlias = new SiteAlias($siteAliasData, '@alias.dev');
        $dockerTransport = new SkprTransport($siteAlias);
        $actual = $dockerTransport->wrap($args);
        $this->assertEquals($expected, implode(' ', $actual));
    }
}
