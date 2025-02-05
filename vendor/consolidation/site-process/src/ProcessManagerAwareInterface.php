<?php
namespace Consolidation\SiteProcess;

/**
 * Inflection interface for the site alias manager.
 */
interface ProcessManagerAwareInterface
{
    /**
     * @param ProcessManager $processManager
     */
    public function setProcessManager(ProcessManager $processManager);

    /**
     * @return ProcessManager
     */
    public function processManager();

    /**
     * @return bool
     */
    public function hasProcessManager();
}
