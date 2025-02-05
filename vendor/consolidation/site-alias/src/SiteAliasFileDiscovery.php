<?php
namespace Consolidation\SiteAlias;

use Symfony\Component\Finder\Finder;

/**
 * Discover alias files named:
 *
 * - sitename.site.yml: contains multiple aliases, one for each of the
 *     environments of 'sitename'.
 *
 * Drush aliases that contain both a site name and an environment
 * (e.g. @site.env) will cause Drush to find the file named after
 * the respective site name and retrieve the specified environment
 * record.
 *
 * Sites may also define a special alias file self.site.yml, which
 * may be stored in the drush/sites directory relative to either
 * the Drupal root or the Composer root of the site. The environments
 * in this file will be merged with the available environments for
 * the element @self, however it is defined.
 */
class SiteAliasFileDiscovery
{
    protected $searchLocations;
    protected $locationFilter;
    protected $depth;

    public function __construct($searchLocations = [], $depth = '<= 1', $locationFilter = null)
    {
        // TODO: Change the default parameter value from 'null' to an empty string
        if ($locationFilter === null) {
            $locationFilter = '';
        }
        $this->locationFilter = $locationFilter;
        $this->searchLocations = $searchLocations;
        $this->depth = $depth;
    }

    /**
     * Add a location that alias files may be found.
     *
     * @param string $path
     * @return $this
     */
    public function addSearchLocation($paths)
    {
        foreach ((array)$paths as $path) {
            if (is_dir($path)) {
                $this->searchLocations[] = $path;
            }
        }
        return $this;
    }

    /**
     * Return all of the paths where alias files may be found.
     * @return string[]
     */
    public function searchLocations()
    {
        return $this->searchLocations;
    }

    public function locationFilter()
    {
        return $this->locationFilter;
    }

    /**
     * Set the search depth for finding alias files
     *
     * @param string|int $depth (@see \Symfony\Component\Finder\Finder::depth)
     * @return $this
     */
    public function depth($depth)
    {
        $this->depth = $depth;
        return $this;
    }

    /**
     * Only search for aliases that are in alias files stored in directories
     * whose basename or key matches the specified location.
     */
    public function filterByLocation($location)
    {
        if (empty($location)) {
            return $this;
        }

        return new SiteAliasFileDiscovery($this->searchLocations(), $this->depth, $location);
    }

    /**
     * Find an alias file SITENAME.site.yml in one
     * of the specified search locations.
     *
     * @param string $siteName
     * @return string[]
     */
    public function find($siteName)
    {
        return $this->searchForAliasFiles("$siteName.site.yml");
    }

    /**
     * Find an alias file SITENAME.site.yml in one
     * of the specified search locations.
     *
     * @param string $siteName
     * @return string|bool
     */
    public function findSingleSiteAliasFile($siteName)
    {
        $matches = $this->find($siteName);
        if (empty($matches)) {
            return false;
        }
        return reset($matches);
    }

    /**
     * Return a list of all SITENAME.site.yml files in any of
     * the search locations.
     *
     * @return string[]
     */
    public function findAllSingleAliasFiles()
    {
        return $this->searchForAliasFiles('*.site.yml');
    }

    /**
     * Return all of the legacy alias files used in previous Drush versions.
     *
     * @return string[]
     */
    public function findAllLegacyAliasFiles()
    {
        return array_merge(
            $this->searchForAliasFiles('*.alias.drushrc.php'),
            $this->searchForAliasFiles('*.aliases.drushrc.php'),
            $this->searchForAliasFiles('aliases.drushrc.php')
        );
    }

    /**
     * Create a Symfony Finder object to search all available search locations
     * for the specified search pattern.
     *
     * @param string $searchPattern
     * @return Finder
     */
    protected function createFinder($searchPattern)
    {
        $finder = new Finder();
        $finder->files()
            ->name($searchPattern)
            ->in($this->searchLocations)
            ->depth($this->depth);
        return $finder;
    }

    /**
     * Return a list of all alias files matching the provided pattern.
     *
     * @param string $searchPattern
     * @return string[]
     */
    protected function searchForAliasFiles($searchPattern)
    {
        if (empty($this->searchLocations)) {
            return [];
        }
        list($match, $site) = $this->splitLocationFromSite($this->locationFilter);
        if (!empty($site)) {
            $searchPattern = str_replace('*', $site, $searchPattern);
        }
        $finder = $this->createFinder($searchPattern);
        $result = [];
        foreach ($finder as $file) {
            $path = $file->getRealPath();
            $result[] = $path;
        }
        // Find every location where the parent directory name matches
        // with the first part of the search pattern.
        // In theory we can use $finder->path() instead. That didn't work well,
        // in practice, though; had trouble correctly escaping the path separators.
        if (!empty($this->locationFilter)) {
            $result = array_filter($result, function ($path) use ($match) {
                return SiteAliasName::locationFromPath($path) === $match;
            });
        }

        return $result;
    }

    /**
     * splitLocationFromSite returns the part of 'site' before the first
     * '.' as the "path match" component, and the part after the first
     * '.' as the "site" component.
     */
    protected function splitLocationFromSite($site)
    {
        $parts = explode('.', $site, 3) + ['', '', ''];

        return array_slice($parts, 0, 2);
    }


    // TODO: Seems like this could just be basename()
    protected function extractKey($basename, $filenameExensions)
    {
        return str_replace($filenameExensions, '', $basename);
    }
}
