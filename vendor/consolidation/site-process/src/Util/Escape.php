<?php
namespace Consolidation\SiteProcess\Util;

use Consolidation\SiteAlias\SiteAliasInterface;
use Symfony\Component\Process\Process;
use Consolidation\Config\Util\Interpolator;
use Symfony\Component\Console\Output\OutputInterface;
use Consolidation\SiteProcess\Util\ShellOperatorInterface;

/**
 * Escape will shell-escape commandline arguments for different platforms.
 */
class Escape
{
    /**
     * argsForSite escapes each argument in an array for the given site.
     */
    public static function argsForSite(SiteAliasInterface $siteAlias, $args)
    {
        return array_map(
            function ($arg) use ($siteAlias) {
                return Escape::forSite($siteAlias, $arg);
            },
            $args
        );
    }

    /**
     * forSite escapes the provided argument for the specified alias record.
     */
    public static function forSite(SiteAliasInterface $siteAlias, $arg)
    {
        return static::shellArg($arg, $siteAlias->os());
    }

    /**
     * shellArg escapes the provided argument for the specified OS
     *
     * @param string|ShellOperatorInterface $arg The argument to escape
     * @param string|null $os The OS to escape for. Optional; defaults to LINUX
     *
     * @return string The escaped string
     */
    public static function shellArg($arg, $os = null)
    {
        // Short-circuit escaping for simple params (keep stuff readable);
        // also skip escaping for shell operators (e.g. &&), which must not
        // be escaped.
        if (($arg instanceof ShellOperatorInterface) || preg_match('|^[a-zA-Z0-9@=.:/_-]*$|', $arg)) {
            return (string) $arg;
        }

        if (static::isWindows($os)) {
            return static::windowsArg($arg);
        }
        return static::linuxArg($arg);
    }

    /**
     * isWindows determines whether the provided OS is Windows.
     *
     * @param string|null $os The OS to escape for.
     *
     * @return boolean
     */
    public static function isWindows($os = null)
    {
        // In most cases, $os will be NULL and PHP_OS will be returned. However,
        // if an OS is specified in $os, return that instead.
        $os = $os ?: PHP_OS;
        return strtoupper(substr($os, 0, 3)) === 'WIN';
    }

    /**
     * linuxArg is the Linux version of escapeshellarg().
     *
     * This is intended to work the same way that escapeshellarg() does on
     * Linux.  If we need to escape a string that will be used remotely on
     * a Linux system, then we need our own implementation of escapeshellarg,
     * because the Windows version behaves differently.
     *
     * Note that we behave somewhat differently than the built-in escapeshellarg()
     * with respect to whitespace replacement in order
     *
     * @param string $arg The argument to escape
     *
     * @return string The escaped string
     */
    public static function linuxArg($arg)
    {
        // For single quotes existing in the string, we will "exit"
        // single-quote mode, add a \' and then "re-enter"
        // single-quote mode.  The result of this is that
        // 'quote' becomes '\''quote'\''
        $arg = preg_replace('/\'/', '\'\\\'\'', $arg);

        // Replace "\t", "\n", "\r", "\0", "\x0B" with a whitespace.
        // Note that this replacement makes Drush's escapeshellarg work differently
        // than the built-in escapeshellarg in PHP on Linux, as these characters
        // usually are NOT replaced. However, this was done deliberately to be more
        // conservative when running _drush_escapeshellarg_linux on Windows
        // (this can happen when generating a command to run on a remote Linux server.)
        //
        // TODO: Perhaps we should only do this if the local system is Windows?
        // n.b. that would be a little more complicated to test.
        $arg = str_replace(["\t", "\n", "\r", "\0", "\x0B"], ' ', $arg);

        // Add surrounding quotes.
        $arg = "'" . $arg . "'";

        return $arg;
    }

    /**
     * windowsArg is the Windows version of escapeshellarg().
     *
     * @param string $arg The argument to escape
     *
     * @return string The escaped string
     */
    public static function windowsArg($arg)
    {
        if ('' === $arg || null === $arg) {
            return '""';
        }
        if (false !== strpos($arg, "\0")) {
            $arg = str_replace("\0", '?', $arg);
        }
        if (!preg_match('/[\/()%!^"<>&|\s]/', $arg)) {
            return $arg;
        }
        // Double up existing backslashes
        $arg = preg_replace('/(\\\\+)$/', '$1$1', $arg);

        // Replacing whitespace for good measure (see comment above).
        $arg = str_replace(["\t", "\n", "\r", "\0", "\x0B"], ' ', $arg);

        $arg = str_replace(['"', '^', '%', '!'], ['""', '"^^"', '"^%"', '"^!"'], $arg);

        // Add surrounding quotes.
        $arg = '"' . $arg . '"';

        return $arg;
    }
}
