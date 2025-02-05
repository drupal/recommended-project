<?php
namespace Consolidation\SiteAlias;

/**
 * Inflection trait for the site alias manager.
 */
trait SiteAliasManagerAwareTrait
{
    protected $siteAliasManager;

    /**
     * @inheritdoc
     */
    public function setSiteAliasManager(SiteAliasManagerInterface $siteAliasManager)
    {
        $this->siteAliasManager = $siteAliasManager;
    }

    /**
     * @return SiteAliasManagerInterface
     */
    public function siteAliasManager()
    {
        return $this->siteAliasManager;
    }

    /**
     * @inheritdoc
     */
    public function hasSiteAliasManager()
    {
        return isset($this->siteAliasManager);
    }
}
