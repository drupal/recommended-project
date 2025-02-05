<?php

namespace Consolidation\SiteProcess;

use Consolidation\SiteProcess\Transport\VagrantTransport;
use PHPUnit\Framework\TestCase;
use Consolidation\SiteAlias\SiteAlias;

class VagrantTransportTest extends TestCase
{
    /**
     * Data provider for testWrap.
     */
    public function wrapTestValues()
    {
        return [
            [
                'vagrant ssh -c ls',
                [
                    'vagrant' => []
                ],
            ]
        ];
    }

    /**
     * @dataProvider wrapTestValues
     */
    public function testWrap($expected, $siteAliasData)
    {
        $siteAlias = new SiteAlias($siteAliasData, '@alias.dev');
        $dockerTransport = new VagrantTransport($siteAlias);
        $actual = $dockerTransport->wrap(['ls']);
        $this->assertEquals($expected, implode(' ', $actual));
    }
}
