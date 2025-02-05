<?php

namespace Consolidation\SiteProcess\Factory;

use Consolidation\SiteAlias\SiteAliasInterface;
use Consolidation\SiteProcess\Transport\DockerComposeTransport;
use Consolidation\Config\ConfigInterface;

/**
 * DockerComposeTransportFactory will create an DockerComposeTransport for
 * applicable site aliases.
 */
class DockerComposeTransportFactory implements TransportFactoryInterface
{
    /**
     * @inheritdoc
     */
    public function check(SiteAliasInterface $siteAlias)
    {
        // TODO: deprecate and eventually remove 'isContainer()', and move the logic here.
        return $siteAlias->isContainer();
    }

    /**
     * @inheritdoc
     */
    public function create(SiteAliasInterface $siteAlias)
    {
        return new DockerComposeTransport($siteAlias);
    }
}
