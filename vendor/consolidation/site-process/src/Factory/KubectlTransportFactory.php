<?php

namespace Consolidation\SiteProcess\Factory;

use Consolidation\SiteAlias\SiteAliasInterface;
use Consolidation\SiteProcess\Transport\KubectlTransport;

/**
 * KubectlTransportFactory will create an KubectlTransport for applicable site aliases.
 */
class KubectlTransportFactory implements TransportFactoryInterface
{
    /**
     * @inheritdoc
     */
    public function check(SiteAliasInterface $siteAlias)
    {
        return $siteAlias->has('kubectl');
    }

    /**
     * @inheritdoc
     */
    public function create(SiteAliasInterface $siteAlias)
    {
        return new KubectlTransport($siteAlias);
    }
}
