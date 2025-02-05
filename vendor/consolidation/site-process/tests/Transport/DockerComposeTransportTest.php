<?php

namespace Consolidation\SiteProcess;

use Consolidation\SiteProcess\Transport\DockerComposeTransport;
use PHPUnit\Framework\TestCase;
use Consolidation\SiteAlias\SiteAlias;

class DockerComposeTransportTest extends TestCase
{
    /**
     * Data provider for testWrap.
     */
    public function wrapTestValues()
    {
        return [
            [
                'docker-compose --project project --project-directory projectDir --file myCompose.yml exec -T --user root drupal ls',
                [
                    'docker' => [
                        'service' => 'drupal',
                        'compose' => [
                            'options' => '--project project --project-directory projectDir --file myCompose.yml'
                        ],
                        'exec' => ['options' => '--user root']
                    ]
                ],
            ],
            [
                'docker-compose exec -T drupal ls',
                [
                    'docker' => [
                        'service' => 'drupal',
                    ]
                ],
            ],
            [
                'docker compose exec -T drupal ls',
                [
                    'docker' => [
                        'service' => 'drupal',
                        'compose' => [
                            'version' => '2',
                        ],
                    ]
                ],
            ],
            [
                'docker-compose exec -T drupal ls',
                [
                    'docker' => [
                        'service' => 'drupal',
                        'compose' => [
                            'version' => '1',
                        ]
                    ]
                ],
            ],
            [
                'docker-compose --project project2 --file myCompose.yml exec -T drupal ls',
                [
                    'docker' => [
                        'service' => 'drupal',
                        'project' => 'project1',
                        'compose' => [
                            'options' => '--project project2 --file myCompose.yml'
                        ]
                    ]
                ],
            ],
            [
                'docker-compose -p project1 --file myCompose.yml exec -T drupal ls',
                [
                    'docker' => [
                        'service' => 'drupal',
                        'project' => 'project1',
                        'compose' => [
                            'options' => '--file myCompose.yml'
                        ]
                    ]
                ],
            ],
        ];
    }

    /**
     * @dataProvider wrapTestValues
     */
    public function testWrap($expected, $siteAliasData)
    {
        $siteAlias = new SiteAlias($siteAliasData, '@alias.dev');
        $dockerTransport = new DockerComposeTransport($siteAlias);
        $actual = $dockerTransport->wrap(['ls']);
        $this->assertEquals($expected, implode(' ', $actual));
    }
}
