<?php
namespace Consolidation\SiteAlias;

use Consolidation\Config\Config;
use Consolidation\Config\ConfigInterface;
use Consolidation\Config\Util\ArrayUtil;
use Consolidation\SiteAlias\Util\FsUtils;

/**
 * Common implementation of some SiteAlias methods.
 */
trait SiteAliasTrait
{
    /**
     * @inheritdoc
     */
    public function hasRoot()
    {
        return $this->has('root');
    }

    /**
     * @inheritdoc
     *
     * @throws \Exception when the alias does not specify a root.
     */
    public function root()
    {
        if (!$this->hasRoot()) {
            throw new \Exception('Site alias ' . $this->name . ' does not specify a root.');
        }
        $root = $this->get('root');
        if ($this->isLocal()) {
            return FsUtils::realpath($root);
        }
        return $root;
    }

    /**
     * @inheritdoc
     */
    public function uri()
    {
        return $this->get('uri');
    }

    /**
     * @inheritdoc
     */
    public function setUri($uri)
    {
        return $this->set('uri', $uri);
    }

    /**
     * @inheritdoc
     */
    public function remoteHostWithUser()
    {
        $result = $this->remoteHost();
        if (!empty($result) && $this->hasRemoteUser()) {
            $result = $this->remoteUser() . '@' . $result;
        }
        return $result;
    }

    /**
     * @inheritdoc
     */
    public function remoteUser()
    {
        return $this->get('user');
    }

    /**
     * @inheritdoc
     */
    public function hasRemoteUser()
    {
        return $this->has('user');
    }

    /**
     * @inheritdoc
     */
    public function remoteHost()
    {
        return $this->get('host');
    }

    /**
     * @inheritdoc
     */
    public function isRemote()
    {
        return $this->has('host');
    }

    /**
     * @inheritdoc
     */
    public function isLocal()
    {
        return !$this->isRemote() && !$this->isContainer();
    }

    /**
     * @inheritdoc
     */
    public function isContainer()
    {
        return $this->has('docker');
    }

    /**
     * @inheritdoc
     */
    public function isNone()
    {
        return empty($this->root()) && $this->isLocal();
    }

    /**
     * @inheritdoc
     */
    public function localRoot()
    {
        if ($this->isLocal() && $this->hasRoot()) {
            return $this->root();
        }

        return false;
    }

    /**
     * os returns the OS that this alias record points to. For local alias
     * records, PHP_OS will be returned. For remote alias records, the
     * value from the `os` element will be returned. If there is no `os`
     * element, then the default assumption is that the remote system is Linux.
     *
     * @return string
     *   Linux
     *   WIN* (e.g. WINNT)
     *   CYGWIN
     *   MINGW* (e.g. MINGW32)
     */
    public function os()
    {
        if ($this->isLocal()) {
            return PHP_OS;
        }
        return $this->get('os', 'Linux');
    }
}
