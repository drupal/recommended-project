<?php
namespace Consolidation\SiteAlias;

/**
 * Parse a string that contains a site alias name, and provide convenience
 * methods to access the parts.
 *
 * When provided by users, aliases must be in one of the following forms:
 *
 *   - @sitename.env: List only sitename and environment.
 *
 *   - @location.sitename.env: List only sitename and environment. Search
 *       only those paths where the name of the folder holding the alias
 *       files matches 'location'. Location terms may only be used when both
 *       the sitename and env are also provided.
 *
 *   - @env: Look up a named environment in instances where the site root
 *       is known (e.g. via cwd). In this form, there is an implicit sitename
 *       'self' which is replaced by the actual site alias name once known.
 *
 *   - @sitename: Provides only the sitename; uses the 'default' environment,
 *       or 'dev' if there is no 'default' (or whatever is there if there is
 *       only one). With this form, the site alias name has no environment
 *       until the appropriate default environment is looked up. This form
 *       is checked only after `@env` returns no matches. This form can NOT
 *       be filtered with a `location.` term.
 *
 * There are also two special aliases that are recognized:
 *
 *   - @self: The current bootstrapped site.
 *
 *   - @none: No alias ('root' and 'uri' unset).
 *
 * The special alias forms have no environment component.
 *
 * When provided to an API, the '@' is optional.
 *
 * Note that @sitename and @env are ambiguous. Aliases in this form
 * (that are not one of the special aliases) will first be assumed
 * to be @env, and may be converted to @sitename later.
 *
 * Note that:
 *
 * - 'sitename' and 'env' MUST NOT contain a '.' (unlike previous
 *     versions of Drush).
 * - Users SHOULD NOT create any environments that have the same name
 *     as any site name (and visa-versa).
 * - All environments in one site record SHOULD be different versions
 *     of the same site (e.g. dev / test / live).
 */
class SiteAliasName
{
    protected $location;
    protected $sitename;
    protected $env;

    /**
     * Match the parts of a regex name.
     */
    const ALIAS_NAME_REGEX = '%^@?([a-zA-Z0-9_-]+)(\.[a-zA-Z0-9_-]+)?(\.[a-zA-Z0-9_-]+)?$%';

    /**
     * Create a new site alias name
     *
     * @param string $item
     * @return SiteAliasName
     */
    public static function parse($item)
    {
        $aliasName = new self();
        $aliasName->doParse($item);
        return $aliasName;
    }

    /**
     * The 'location' of an alias file is defined as being the name
     * of the immediate parent of the alias file.  e.g. the path
     * '$HOME/.drush/sites/isp/mysite.site.yml' would have a location
     * of 'isp' and a sitename of 'mysite'. The environments of the site
     * are defined by the alias contents.
     *
     * @param type $path
     * @return type
     */
    public static function locationFromPath($path)
    {
        $location = ltrim(basename(dirname($path)), '.');
        if (($location === 'sites') || ($location === 'drush')) {
            return '';
        }
        return $location;
    }

    /**
     * Creae a SiteAliasName object from an alias name string.
     *
     * @param string $sitename The alias name for the site.
     * @param string $env The name for the site's environment.
     * @param string $location The location filter for the site.
     */
    public function __construct($sitename = null, $env = null, $location = null)
    {
        $this->location = $location;
        $this->sitename = $sitename;
        $this->env = $env;
    }

    /**
     * Convert an alias name back to a string.
     *
     * @return string
     */
    public function __toString()
    {
        $parts = [ $this->sitename() ];
        if ($this->hasLocation()) {
            array_unshift($parts, $this->location());
        }
        if ($this->hasEnv()) {
            $parts[] = $this->env();
        }
        return '@' . implode('.', $parts);
    }

    /**
     * Determine whether or not the provided name is an alias name.
     *
     * @param string $aliasName
     * @return bool
     */
    public static function isAliasName($aliasName)
    {
        // Alias names provided by users must begin with '@'
        if (empty($aliasName) || ($aliasName[0] != '@')) {
            return false;
        }
        return preg_match(self::ALIAS_NAME_REGEX, $aliasName);
    }

    /**
     * Return the sitename portion of the alias name. By definition,
     * every alias must have a sitename. If the site name is implicit,
     * then 'self' is assumed.
     *
     * @return string
     */
    public function sitename()
    {
        if (empty($this->sitename)) {
            return 'self';
        }
        return $this->sitename;
    }

    /**
     * Return the sitename portion of the alias name. By definition,
     * every alias must have a sitename. If the site name is implicit,
     * then 'self' is assumed.
     *
     * @return string
     */
    public function sitenameWithLocation()
    {
        if (empty($this->sitename)) {
            return 'self';
        }
        return (empty($this->location) ? '' : $this->location . '.') . $this->sitename;
    }

    /**
     * Set the sitename portion of the alias name
     *
     * @param string $sitename
     */
    public function setSitename($sitename)
    {
        $this->sitename = $sitename;
        return $this;
    }

    /**
     * In general, all aliases have a sitename. The time when one will not
     * is when an environment name `@env` is used as a shortcut for `@self.env`
     *
     * @return bool
     */
    public function hasSitename()
    {
        return !empty($this->sitename);
    }

    /**
     * Return true if this alias name contains an 'env' portion.
     *
     * @return bool
     */
    public function hasEnv()
    {
        return !empty($this->env);
    }

    /**
     * Set the environment portion of the alias name.
     *
     * @param string
     */
    public function setEnv($env)
    {
        $this->env = $env;
        return $this;
    }

    /**
     * Return the 'env' portion of the alias name.
     *
     * @return string
     */
    public function env()
    {
        return $this->env;
    }

    /**
     * Return true if this alias name contains a 'location' portion
     * @return bool
     */
    public function hasLocation()
    {
        return !empty($this->location);
    }

    /**
     * Set the 'loation' portion of the alias name.
     * @param string $location
     */
    public function setLocation($location)
    {
        $this->location = $location;
        return $this;
    }

    /**
     * Return the 'location' portion of the alias name.
     *
     * @param string
     */
    public function location()
    {
        return $this->location;
    }

    /**
     * Return true if this alias name is the 'self' alias.
     *
     * @return bool
     */
    public function isSelf()
    {
        return ($this->sitename == 'self') && !isset($this->env);
    }

    /**
     * Return true if this alias name is the 'none' alias.
     */
    public function isNone()
    {
        return ($this->sitename == 'none') && !isset($this->env);
    }

    /**
     * Convert the parts of an alias name to its various component parts.
     *
     * @param string $aliasName a string representation of an alias name.
     */
    protected function doParse($aliasName)
    {
        // Example contents of $matches:
        //
        // - a.b:
        //     [
        //       0 => 'a.b',
        //       1 => 'a',
        //       2 => '.b',
        //     ]
        //
        // - a:
        //     [
        //       0 => 'a',
        //       1 => 'a',
        //     ]
        if (!preg_match(self::ALIAS_NAME_REGEX, $aliasName, $matches)) {
            return false;
        }

        // Get rid of $matches[0]
        array_shift($matches);

        // If $matches contains only one item1, then assume the alias name
        // contains only the environment.
        if (count($matches) == 1) {
            return $this->processSingleItem($matches[0]);
        }

        // If there are three items, then the first is the location.
        if (count($matches) == 3) {
            $this->location = trim(array_shift($matches), '.');
        }

        // The sitename and env follow the location.
        $this->sitename = trim(array_shift($matches), '.');
        $this->env = trim(array_shift($matches), '.');
        return true;
    }

    /**
     * Process an alias name provided as '@sitename'.
     *
     * @param string $sitename
     * @return true
     */
    protected function processSingleItem($item)
    {
        if ($this->isSpecialAliasName($item)) {
            $this->setSitename($item);
            return true;
        }
        $this->sitename = '';
        $this->env = $item;
        return true;
    }

    /**
     * Determine whether the requested name is a special alias name.
     *
     * @param string $item
     * @return boolean
     */
    protected function isSpecialAliasName($item)
    {
        return ($item == 'self') || ($item == 'none');
    }
}
