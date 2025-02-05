<?php

namespace Consolidation\SiteProcess\Factory;

use Consolidation\SiteAlias\SiteAliasInterface;
use Consolidation\SiteProcess\Transport\VagrantTransport;

/**
 * VagrantTransportFactory will create a VagrantTransport for applicable site aliases.
 */
class VagrantTransportFactory implements TransportFactoryInterface
{
    /**
     * @inheritdoc
     */
    public function check(SiteAliasInterface $siteAlias)
    {
        return $siteAlias->has('vagrant');
    }

    /**
     * @inheritdoc
     */
    public function create(SiteAliasInterface $siteAlias)
    {
        return new VagrantTransport($siteAlias);
    }
}
