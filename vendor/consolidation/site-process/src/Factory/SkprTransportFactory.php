<?php

namespace Consolidation\SiteProcess\Factory;

use Consolidation\SiteAlias\SiteAliasInterface;
use Consolidation\SiteProcess\Transport\SkprTransport;

/**
 * SkprTransportFactory will create an SkprTransport for applicable site aliases.
 */
class SkprTransportFactory implements TransportFactoryInterface
{
    /**
     * @inheritdoc
     */
    public function check(SiteAliasInterface $siteAlias)
    {
        return $siteAlias->has('skpr');
    }

    /**
     * @inheritdoc
     */
    public function create(SiteAliasInterface $siteAlias)
    {
        return new SkprTransport($siteAlias);
    }
}
