<?php
namespace Consolidation\SiteProcess\Util;

use Symfony\Component\Process\Process;
use Consolidation\Config\Util\Interpolator;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Output\NullOutput;

/**
 * RealtimeOutput can be provided to a process object when you want
 * to display the output of the running command as it is being produced.
 */
class RealtimeOutputHandler
{
    protected $stdout;
    protected $stderr;
    protected $stdoutMarker = '';
    protected $stderrMarker = '';

    /**
     * Provide the output streams to use for stdout and stderr
     */
    const MARKER_ERR = '> ';

    public function __construct(OutputInterface $stdout, OutputInterface $stderr)
    {
        $this->stdout = $stdout;
        $this->stderr = $stderr;

        $this->stdoutMarker = '';
        $this->stderrMarker = self::MARKER_ERR;
    }

    /**
     * This gives us an opportunity to adapt to the settings of the
     * process object (e.g. do we need to do anything differently if
     * it is in tty mode, etc.)
     */
    public function configure(Process $process)
    {
        return $this;
    }

    /**
     * setStderrMarker defines the string that should be added at
     * the beginning of every line of stderr that is printed.
     */
    public function setStderrMarker($marker)
    {
        $this->stderrMarker = $marker;
        return $this;
    }

    /**
     * setStdoutMarker defines the string that should be added at
     * the beginning of every line of stdout that is printed.
     */
    public function setStdoutMarker($marker)
    {
        $this->stdoutMarker = $marker;
        return $this;
    }

    /**
     * hideStdout overrides whatever was formerly stored in $this->stdout
     * with a null output buffer so that none of the standard output data
     * is visible.
     */
    public function hideStdout()
    {
        $this->stdout = new NullOutput();
        $this->stdoutMarker = '';
        return $this;
    }

    /**
     * hideStderr serves the same function as hideStdout, but for the
     * standard error stream. Note that it is not useful to unconditionally
     * call both hideStdout and hideStderr; if no output is desired, then
     * the RealtimeOutputHandler should not be used.
     */
    public function hideStderr()
    {
        $this->stderr = new NullOutput();
        $this->stderrMarker = '';
        return $this;
    }

    /**
     * If this object is used as a callable, then run 'handleOutput'.
     */
    public function __invoke($type, $buffer)
    {
        $this->handleOutput($type, $buffer);
    }

    /**
     * Helper method when you want real-time output from a Process call.
     * @param string $type
     * @param string $buffer
     */
    public function handleOutput($type, $buffer)
    {
        if (Process::ERR === $type) {
            $this->stderr->write($this->addMarker($buffer, $this->stderrMarker), false, OutputInterface::OUTPUT_RAW);
        } else {
            $this->stdout->write($this->addMarker($buffer, $this->stdoutMarker), false, OutputInterface::OUTPUT_RAW);
        }
    }

    /**
     * Make sure that every line in $buffer begins with a MARKER_ERR.
     */
    protected function addMarker($buffer, $marker)
    {
        // Exit early if there is no marker to add
        if (empty($marker)) {
            return $buffer;
        }
        // Add a marker on the beginning of every line.
        return $marker . rtrim(implode("\n" . $marker, explode("\n", $buffer)), $marker);
    }
}
