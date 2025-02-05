<?php

namespace Consolidation\SiteProcess\Factory;

use Consolidation\SiteAlias\SiteAliasInterface;
use Consolidation\SiteProcess\Transport\SshTransport;
use Consolidation\Config\ConfigInterface;

/**
 * SshTransportFactory will create an SshTransport for applicable site aliases.
 */
class SshTransportFactory implements TransportFactoryInterface
{
    /**
     * @inheritdoc
     */
    public function check(SiteAliasInterface $siteAlias)
    {
        // TODO: deprecate and eventually remove 'isRemote()', and move the logic here.
        return $siteAlias->isRemote();
    }

    /**
     * @inheritdoc
     */
    public function create(SiteAliasInterface $siteAlias)
    {
        return new SshTransport($siteAlias);
    }
}
