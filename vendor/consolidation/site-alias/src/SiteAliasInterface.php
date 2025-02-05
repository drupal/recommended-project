<?php
namespace Consolidation\SiteAlias;

use Consolidation\Config\Config;
use Consolidation\Config\ConfigInterface;
use Consolidation\Config\Util\ArrayUtil;

/**
 * An alias record is a configuration record containing well-known items.
 *
 * NOTE: SiteAlias is implemented as a Config subclass; however, it
 * should not be used as a config. (A better implementation would be
 * "hasa" config, but that is less convenient, as we want all of the
 * same capabilities as a config object).
 *
 * If using an alias record as config is desired, use the 'exportConfig()'
 * method.
 *
 * Example remote alias:
 *
 * ---
 * host: www.myisp.org
 * user: www-data
 * root: /path/to/drupal
 * uri: mysite.org
 *
 * Example local alias with global and command-specific options:
 *
 * ---
 * root: /path/to/drupal
 * uri: mysite.org
 * options:
 *   no-interaction: true
 * command:
 *   user:
 *     login:
 *       options:
 *         name: superuser
 */
interface SiteAliasInterface extends ConfigInterface
{
    /**
     * Return the name of this alias record.
     *
     * @return string
     */
    public function name();

    /**
     * Determine whether this alias has a root.
     */
    public function hasRoot();

    /**
     * Get the root
     */
    public function root();

    /**
     * Get the uri
     */
    public function uri();

    /**
     * Record the uri
     *
     * @param string $uri
     */
    public function setUri($uri);

    /**
     * Return user@host, or just host if there is no user. Returns
     * an empty string if there is no host.
     *
     * @return string
     */
    public function remoteHostWithUser();

    /**
     * Get the remote user
     */
    public function remoteUser();

    /**
     * Return true if this alias record has a remote user
     */
    public function hasRemoteUser();

    /**
     * Get the remote host
     */
    public function remoteHost();

    /**
     * Return true if this alias record has a remote host that is not
     * the local host
     *
     * @internal
     */
    public function isRemote();

    /**
     * Return true if the alias record use the Docker Compose transport.
     *
     * @internal
     *
     * @return bool
     */
    public function isContainer();

    /**
     * Return true if this alias record is for the local system
     */
    public function isLocal();

    /**
     * Determine whether this alias does not represent any site. An
     * alias record must either be remote or have a root.
     */
    public function isNone();

    /**
     * Return the 'root' element of this alias if this alias record
     * is local.
     */
    public function localRoot();

    /**
     * Export the configuration values in this alias record, and reconfigure
     * them so that the layout matches that of the global configuration object.
     */
    public function exportConfig();
}
