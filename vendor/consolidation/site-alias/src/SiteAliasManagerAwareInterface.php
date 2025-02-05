<?php
namespace Consolidation\SiteAlias;

/**
 * Inflection interface for the site alias manager.
 */
interface SiteAliasManagerAwareInterface
{
    /**
     * @param SiteAliasManager $siteAliasManager
     */
    public function setSiteAliasManager(SiteAliasManagerInterface $siteAliasManager);

    /**
     * @return SiteAliasManagerInterface
     */
    public function siteAliasManager();

    /**
     * @return bool
     */
    public function hasSiteAliasManager();
}
