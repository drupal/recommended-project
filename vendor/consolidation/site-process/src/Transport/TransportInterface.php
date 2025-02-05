<?php

namespace Consolidation\SiteProcess\Transport;

use Consolidation\SiteProcess\SiteProcess;

/**
 * Transports know how to wrap a command such that it runs on a remote system
 * via some other command.
 *
 * There is always a transport for every factory, and visa-versa.
 *
 * @see Consolidation\SiteProcess\Factory\TransportFactoryInterface
 */
interface TransportInterface
{
    /**
     * Configure ourselves based on the settings of the process object
     * (e.g. isTty()).
     *
     * @param \Consolidation\SiteProcess\SiteProcess $process
     */
    public function configure(SiteProcess $process);

    /**
     * wrapWithTransport examines the provided site alias; if it is a local
     * alias, then the provided arguments are returned unmodified. If the
     * alias points at a remote system, though, then the arguments are
     * escaped and wrapped in an appropriate ssh command.
     *
     * @param array $args arguments provided by caller.
     * @return array command and arguments to execute.
     */
    public function wrap($args);

    /**
     * addChdir adds an appropriate 'chdir' / 'cd' command for the transport.
     */
    public function addChdir($cd, $args);
}
