<?php
namespace Consolidation\SiteAlias;

use Consolidation\Config\Loader\ConfigProcessor;
use Dflydev\DotAccessData\Util as DotAccessDataUtil;

/**
 * Discover alias files:
 *
 * - sitename.site.yml: contains multiple aliases, one for each of the
 *     environments of 'sitename'.
 */
class SiteAliasFileLoader
{
    /**
     * @var SiteAliasFileDiscovery
     */
    protected $discovery;

    /**
     * @var array
     */
    protected $referenceData;

    /**
     * @var array
     */
    protected $loader;

    /**
     * @var string
     */
    protected $root;

    /**
     * SiteAliasFileLoader constructor
     *
     * @param SiteAliasFileDiscovery|null $discovery
     */
    public function __construct($discovery = null)
    {
        $this->discovery = $discovery ?: new SiteAliasFileDiscovery();
        $this->referenceData = [];
        $this->loader = [];
    }

    /**
     * Allow configuration data to be used in replacements in the alias file.
     */
    public function setReferenceData($data)
    {
        $this->referenceData = $data;
    }

    /**
     * Allow 'self.site.yml' to be applied to any alias record found.
     */
    public function setRoot($root)
    {
        $this->root = $root;
    }

    /**
     * Add a search location to our discovery object.
     *
     * @param string $path
     *
     * @return $this
     */
    public function addSearchLocation($path)
    {
        $this->discovery()->addSearchLocation($path);
        return $this;
    }

    /**
     * Return our discovery object.
     *
     * @return SiteAliasFileDiscovery
     */
    public function discovery()
    {
        return $this->discovery;
    }

    /**
     * Load the file containing the specified alias name.
     *
     * @param SiteAliasName $aliasName
     *
     * @return SiteAlias|false
     */
    public function load(SiteAliasName $aliasName)
    {
        // First attempt to load a sitename.site.yml file for the alias.
        $aliasRecord = $this->loadSingleAliasFile($aliasName);
        if ($aliasRecord) {
            return $aliasRecord;
        }

        // If aliasname was provides as @site.env and we did not find it,
        // then we are done.
        if ($aliasName->hasSitename()) {
            return false;
        }

        // If $aliasName was provided as `@foo` (`hasSitename()` returned `false`
        // above), then this was interpreted as `@self.foo` when we searched
        // above. If we could not find an alias record for `@self.foo`, then we
        // will try to search again, this time with the assumption that `@foo`
        // might be `@foo.<default>`, where `<default>` is the default
        // environment for the specified site. Note that in this instance, the
        // sitename will be found in $aliasName->env().
        $sitename = $aliasName->env();
        return $this->loadDefaultEnvFromSitename($sitename);
    }

    /**
     * Given only a site name, load the default environment from it.
     */
    protected function loadDefaultEnvFromSitename($sitename)
    {
        $path = $this->discovery()->findSingleSiteAliasFile($sitename);
        if (!$path) {
            return false;
        }
        $data = $this->loadSiteDataFromPath($path);
        if (!$data) {
            return false;
        }
        $env = $this->getDefaultEnvironmentName($data);

        $aliasName = new SiteAliasName($sitename, $env);
        $processor = new ConfigProcessor();
        return $this->fetchSiteAliasFromSiteAliasData($aliasName, $processor, $data);
    }

    /**
     * Return a list of all site aliases loadable from any findable path.
     *
     * @return SiteAlias[]
     */
    public function loadAll()
    {
        $result = [];
        $paths = $this->discovery()->findAllSingleAliasFiles();
        foreach ($paths as $path) {
            $aliasRecords = $this->loadSingleSiteAliasFileAtPath($path);
            if ($aliasRecords) {
                foreach ($aliasRecords as $aliasRecord) {
                    $this->storeSiteAliasInResut($result, $aliasRecord);
                }
            }
        }
        ksort($result);
        return $result;
    }

    /**
     * Return a list of all available alias files. Does not include
     * legacy files.
     *
     * @param string $location Only consider alias files in the specified location.
     * @return string[]
     */
    public function listAll($location = '')
    {
        return $this->discovery()->filterByLocation($location)->findAllSingleAliasFiles();
    }

    /**
     * Given an alias name that might represent multiple sites,
     * return a list of all matching alias records. If nothing was found,
     * or the name represents a single site + env, then we take
     * no action and return `false`.
     *
     * @param string $sitename The site name to return all environments for.
     * @return SiteAlias[]|false
     */
    public function loadMultiple($sitename, $location = null)
    {
        $result = [];
        foreach ($this->discovery()->filterByLocation($location)->find($sitename) as $path) {
            if ($siteData = $this->loadSiteDataFromPath($path)) {
                $location = SiteAliasName::locationFromPath($path);
                // Convert the raw array into a list of alias records.
                $result = array_merge(
                    $result,
                    $this->createSiteAliassFromSiteData($sitename, $siteData, $location)
                );
            }
        }
        return $result;
    }

    /**
     * Given a location, return all alias files located there.
     *
     * @param string $location The location to filter.
     * @return SiteAlias[]
     */
    public function loadLocation($location)
    {
        $result = [];
        foreach ($this->listAll($location) as $path) {
            if ($siteData = $this->loadSiteDataFromPath($path)) {
                $location = SiteAliasName::locationFromPath($path);
                $sitename = $this->siteNameFromPath($path);
                // Convert the raw array into a list of alias records.
                $result = array_merge(
                    $result,
                    $this->createSiteAliassFromSiteData($sitename, $siteData, $location)
                );
            }
        }
        return $result;
    }

    /**
     * @param array $siteData list of sites with its respective data
     *
     * @param SiteAliasName $aliasName The name of the record being created
     * @param $siteData An associative array of envrionment => site data
     * @return SiteAlias[]
     */
    protected function createSiteAliassFromSiteData($sitename, $siteData, $location = '')
    {
        $result = [];
        if (!is_array($siteData) || empty($siteData)) {
            return $result;
        }
        foreach ($siteData as $envName => $data) {
            if (is_array($data) && $this->isValidEnvName($envName)) {
                $aliasName = new SiteAliasName($sitename, $envName, $location);

                $processor = new ConfigProcessor();
                $oneRecord = $this->fetchSiteAliasFromSiteAliasData($aliasName, $processor, $siteData);
                $this->storeSiteAliasInResut($result, $oneRecord);
            }
        }
        return $result;
    }

    /**
     * isValidEnvName determines if a given entry should be skipped or not
     * (e.g. the "common" entry).
     *
     * @param string $envName The environment name to test
     */
    protected function isValidEnvName($envName)
    {
        return $envName != 'common';
    }

    /**
     * Store an alias record in a list. If the alias record has
     * a known name, then the key of the list will be the record's name.
     * Otherwise, append the record to the end of the list with
     * a numeric index.
     *
     * @param &SiteAlias[] $result list of alias records
     * @param SiteAlias $aliasRecord one more alias to store in the result
     */
    protected function storeSiteAliasInResut(&$result, SiteAlias $aliasRecord)
    {
        if (!$aliasRecord) {
            return;
        }
        $key = $aliasRecord->name();
        if (empty($key)) {
            $result[] = $aliasRecord;
            return;
        }
        $result[$key] = $aliasRecord;
    }

    /**
     * If the alias name is '@sitename', or if it is '@sitename.env', then
     * look for a sitename.site.yml file that contains it. We also handle
     * '@location.sitename.env' here as well.
     *
     * @param SiteAliasName $aliasName
     *
     * @return SiteAlias|false
     */
    protected function loadSingleAliasFile(SiteAliasName $aliasName)
    {
        // Check to see if the appropriate sitename.alias.yml file can be
        // found. Return if it cannot.
        $path = $this->discovery()
            ->filterByLocation($aliasName->location())
            ->findSingleSiteAliasFile($aliasName->sitename());
        if (!$path) {
            return false;
        }
        return $this->loadSingleAliasFileWithNameAtPath($aliasName, $path);
    }

    /**
     * Given only the path to an alias file `site.alias.yml`, return all
     * of the alias records for every environment stored in that file.
     *
     * @param string $path
     * @return SiteAlias[]
     */
    protected function loadSingleSiteAliasFileAtPath($path)
    {
        $sitename = $this->siteNameFromPath($path);
        $location = SiteAliasName::locationFromPath($path);
        if ($siteData = $this->loadSiteDataFromPath($path)) {
            return $this->createSiteAliassFromSiteData($sitename, $siteData, $location);
        }
        return false;
    }

    /**
     * Given the path to a single site alias file `site.alias.yml`,
     * return the `site` part.
     *
     * @param string $path
     */
    protected function siteNameFromPath($path)
    {
        return $this->basenameWithoutExtension($path, '.site.yml');

// OR:
//        $filename = basename($path);
//        return preg_replace('#\..*##', '', $filename);
    }

    /**
     * Chop off the `aliases.yml` or `alias.yml` part of a path. This works
     * just like `basename`, except it will throw if the provided path
     * does not end in the specified extension.
     *
     * @param string $path
     * @param string $extension
     * @return string
     * @throws \Exception
     */
    protected function basenameWithoutExtension($path, $extension)
    {
        $result = basename($path, $extension);
        // It is an error if $path does not end with site.yml
        if ($result == basename($path)) {
            throw new \Exception("$path must end with '$extension'");
        }
        return $result;
    }

    /**
     * Given an alias name and a path, load the data from the path
     * and process it as needed to generate the alias record.
     *
     * @param SiteAliasName $aliasName
     * @param string $path
     * @return SiteAlias|false
     */
    protected function loadSingleAliasFileWithNameAtPath(SiteAliasName $aliasName, $path)
    {
        $data = $this->loadSiteDataFromPath($path);
        if (!$data) {
            return false;
        }
        $processor = new ConfigProcessor();
        return $this->fetchSiteAliasFromSiteAliasData($aliasName, $processor, $data);
    }

    /**
     * Load the yml from the given path
     *
     * @param string $path
     * @return array|bool
     */
    protected function loadSiteDataFromPath($path)
    {
        $data = $this->loadData($path);
        if (!$data) {
            return false;
        }
        $selfSiteAliases = $this->findSelfSiteAliases($data, $path);
        $data = array_merge($selfSiteAliases, $data);
        return $data;
    }

    /**
     * Given an array of site aliases, find the first one that is
     * local (has no 'host' item) and also contains a 'self.site.yml' file.
     * @param array $data
     * @return array
     */
    protected function findSelfSiteAliases($site_aliases, $path)
    {
        foreach ($site_aliases as $site => $data) {
            if (!isset($data['host']) && isset($data['root'])) {
                $data = $this->loadSelfSiteData($data['root']);
                if (!empty($data)) {
                    return $data;
                }
            }
        }

        return $this->loadSelfSiteData($this->root);
    }

    /**
     * Check to see if there is a 'drush/sites/self.site.yml' file at
     * the provided root, or one directory up from there.
     */
    protected function loadSelfSiteData($root)
    {
        if (!$root) {
            return [];
        }
        foreach (['.', '..'] as $relative_path) {
            $candidate = $root . '/' . $relative_path . '/drush/sites/self.site.yml';
            if (file_exists($candidate)) {
                return $this->loadData($candidate);
            }
        }
        return [];
    }

    /**
     * Load the contents of the specified file.
     *
     * @param string $path Path to file to load
     * @return array
     */
    protected function loadData($path)
    {
        if (empty($path) || !file_exists($path)) {
            return [];
        }
        $loader = $this->getLoader(pathinfo($path, PATHINFO_EXTENSION));
        if (!$loader) {
            return [];
        }
        return $loader->load($path);
    }

    /**
     * @return DataFileLoaderInterface
     */
    public function getLoader($extension)
    {
        if (!isset($this->loader[$extension])) {
            return null;
        }
        return $this->loader[$extension];
    }

    public function addLoader($extension, DataFileLoaderInterface $loader)
    {
        $this->loader[$extension] = $loader;
    }

    /**
     * Given an array containing site alias data, return an alias record
     * containing the data for the requested record. If there is a 'common'
     * section, then merge that in as well.
     *
     * @param SiteAliasName $aliasName the alias we are loading
     * @param array $data
     *
     * @return SiteAlias|false
     */
    protected function fetchSiteAliasFromSiteAliasData(SiteAliasName $aliasName, ConfigProcessor $processor, array $data)
    {
        $data = $this->adjustIfSingleAlias($data);
        $env = $this->getEnvironmentName($aliasName, $data);
        $env_data = $this->getRequestedEnvData($data, $env);
        if (!$env_data) {
            return false;
        }

        // Add the 'common' section if it exists.
        if ($this->siteEnvExists($data, 'common')) {
            $processor->add($data['common']);
        }

        // Then add the data from the desired environment.
        $processor->add($env_data);

        // Export the combined data and create an SiteAlias object to manage it.
        return new SiteAlias($processor->export($this->referenceData + ['env-name' => $env]), '@' . $aliasName->sitenameWithLocation(), $env);
    }

    /**
     * getRequestedEnvData fetches the data for the specified environment
     * from the provided site record data.
     *
     * @param array $data The site alias data
     * @param string $env The name of the environment desired
     * @return array|false
     */
    protected function getRequestedEnvData(array $data, $env)
    {
        // If the requested environment exists, we will use it.
        if ($this->siteEnvExists($data, $env)) {
            return $data[$env];
        }

        // If there is a wildcard environment, then return that instead.
        if ($this->siteEnvExists($data, '*')) {
            return $data['*'];
        }

        return false;
    }

    /**
     * Determine whether there is a valid-looking environment '$env' in the
     * provided site alias data.
     *
     * @param array $data
     * @param string $env
     * @return bool
     */
    protected function siteEnvExists(array $data, $env)
    {
        return (
            is_array($data) &&
            isset($data[$env]) &&
            is_array($data[$env])
        );
    }

    /**
     * Adjust the alias data for a single-site alias. Usually, a .yml alias
     * file will contain multiple entries, one for each of the environments
     * of an alias. If there are no environments
     *
     * @param array $data
     * @return array
     */
    protected function adjustIfSingleAlias($data)
    {
        if (!$this->detectSingleAlias($data)) {
            return $data;
        }

        $result = [
            'default' => $data,
        ];

        return $result;
    }

    /**
     * A single-environment alias looks something like this:
     *
     *   ---
     *   root: /path/to/drupal
     *   uri: https://mysite.org
     *
     * A multiple-environment alias looks something like this:
     *
     *   ---
     *   default: dev
     *   dev:
     *     root: /path/to/dev
     *     uri: https://dev.mysite.org
     *   stage:
     *     root: /path/to/stage
     *     uri: https://stage.mysite.org
     *
     * The differentiator between these two is that the multi-environment
     * alias always has top-level elements that are associative arrays, and
     * the single-environment alias never does.
     *
     * @param array $data
     * @return bool
     */
    protected function detectSingleAlias($data)
    {
        foreach ($data as $key => $value) {
            if (is_array($value) && DotAccessDataUtil::isAssoc($value)) {
                return false;
            }
        }
        return true;
    }

    /**
     * Return the name of the environment requested.
     *
     * @param SiteAliasName $aliasName the alias we are loading
     * @param array $data
     *
     * @return string
     */
    protected function getEnvironmentName(SiteAliasName $aliasName, array $data)
    {
        // If the alias name specifically mentions the environment
        // to use, then return it.
        if ($aliasName->hasEnv()) {
            return $aliasName->env();
        }
        return $this->getDefaultEnvironmentName($data);
    }

    /**
     * Given a data array containing site alias environments, determine which
     * envirionmnet should be used as the default environment.
     *
     * @param array $data
     * @return string
     */
    protected function getDefaultEnvironmentName(array $data)
    {
        // If there is an entry named 'default', it will either contain the
        // name of the environment to use by default, or it will itself be
        // the default environment.
        if (isset($data['default'])) {
            return is_array($data['default']) ? 'default' : $data['default'];
        }
        // If there is an environment named 'dev', it will be our default.
        if (isset($data['dev'])) {
            return 'dev';
        }
        // If we don't know which environment to use, just take the first one.
        $keys = array_keys($data);
        return reset($keys);
    }
}
