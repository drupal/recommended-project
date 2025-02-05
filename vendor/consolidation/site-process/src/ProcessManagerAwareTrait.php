<?php
namespace Consolidation\SiteProcess;

/**
 * Inflection trait for the site alias manager.
 */
trait ProcessManagerAwareTrait
{
    protected $processManager;

    /**
     * @inheritdoc
     */
    public function setProcessManager(ProcessManager $processManager)
    {
        $this->processManager = $processManager;
    }

    /**
     * @return ProcessManager
     */
    public function processManager()
    {
        return $this->processManager;
    }

    /**
     * @inheritdoc
     */
    public function hasProcessManager()
    {
        return isset($this->processManager);
    }
}
