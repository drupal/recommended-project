<?php
namespace Consolidation\SiteAlias;

/**
 * Site Alias manager
 */
interface SiteAliasManagerInterface
{
    /**
     * Return all of the paths where alias files may be found.
     * @return string[]
     */
    public function searchLocations();

    /**
     * Get an alias record by name, or convert a site specification
     * into an alias record via the site alias spec parser. If a
     * simple alias name is provided (e.g. '@alias'), it is interpreted
     * as a sitename, and the default environment for that site is returned.
     *
     * @param string $name Alias name or site specification
     *
     * @return SiteAlias|false
     */
    public function get($name);

    /**
     * Get the '@self' alias record.
     *
     * @return SiteAlias
     */
    public function getSelf();

    /**
     * Get an alias record from a name. Does not accept site specifications.
     *
     * @param string $aliasName alias name
     *
     * @return SiteAlias
     */
    public function getAlias($aliasName);

    /**
     * Given a simple alias name, e.g. '@alias', returns all of the
     * environments in the specified site.
     *
     * If the provided name is a site specification et. al.,
     * then this method will return 'false'.
     *
     * @param string $name Alias name
     * @return SiteAlias[]|false
     */
    public function getMultiple($name = '');

    /**
     * Return the paths to all alias files in all search locations known
     * to the alias manager.
     *
     * @return string[]
     */
    public function listAllFilePaths($location = '');
}
