<?php
namespace Consolidation\SiteAlias;

/**
 * Site Alias manager methods used to set up the object.
 */
interface SiteAliasManagerInitializationInterface
{
    /**
     * Allow configuration data to be used in replacements in the alias file.
     */
    public function setReferenceData($data);

    /**
     * Inject the root of the selected site
     *
     * @param string $root
     * @return $this
     */
    public function setRoot($root);

    /**
     * Add a search location to our site alias discovery object.
     *
     * @param string $path
     *
     * @return $this
     */
    public function addSearchLocation($path);

    /**
     * Add search locations to our site alias discovery object.
     *
     * @param array $paths Any path provided in --alias-path option
     *   or drush.path.alias-path configuration item.
     *
     * @return $this
     */
    public function addSearchLocations(array $paths);

    /**
     * Force-set the current @self alias.
     *
     * @param SiteAlias $selfSiteAlias
     * @return $this
     */
    public function setSelf(SiteAlias $selfSiteAlias);
}
