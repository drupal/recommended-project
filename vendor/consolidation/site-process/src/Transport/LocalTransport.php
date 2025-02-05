<?php

namespace Consolidation\SiteProcess\Transport;

use Consolidation\SiteProcess\SiteProcess;

/**
 * LocalTransport just runs the command on the local system.
 */
class LocalTransport implements TransportInterface
{
    /**
     * @inheritdoc
     */
    public function configure(SiteProcess $process)
    {
        $process->setWorkingDirectoryLocal($process->getWorkingDirectory());
    }

    /**
     * @inheritdoc
     */
    public function wrap($args)
    {
        return $args;
    }

    /**
     * @inheritdoc
     */
    public function addChdir($cd, $args)
    {
        return $args;
    }
}
