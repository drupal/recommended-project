<?php
namespace Consolidation\SiteAlias\Util;

use Symfony\Component\Filesystem\Filesystem;

class FsUtils
{
    /**
     * Returns canonicalized absolute pathname.
     *
     * The difference between this and PHP's realpath() is that this will
     * return the original path even if it doesn't exist.
     *
     * @param string $path
     *   The path being checked.
     *
     * @return string
     *   The canonicalized absolute pathname.
     */
    public static function realpath($path)
    {
        $realpath = realpath($path);
        return $realpath ?: $path;
    }
}
