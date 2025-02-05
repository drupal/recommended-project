<?php
namespace Consolidation\SiteProcess;

use Consolidation\SiteAlias\SiteAliasInterface;
use Consolidation\SiteProcess\Transport\DockerComposeTransport;
use Consolidation\SiteProcess\Util\ArgumentProcessor;
use Consolidation\SiteProcess\Transport\LocalTransport;
use Consolidation\SiteProcess\Transport\SshTransport;
use Consolidation\SiteProcess\Transport\TransportInterface;
use Consolidation\Config\Util\Interpolator;
use Consolidation\SiteProcess\Util\Shell;
use Consolidation\SiteProcess\Util\ShellOperatorInterface;
use Consolidation\SiteProcess\Util\Escape;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

/**
 * A wrapper around Symfony Process that uses site aliases
 * (https://github.com/consolidation/site-alias)
 *
 * - Interpolate arguments using values from the alias
 *   e.g. `$process = new SiteProcess($alias, ['git', '-C', '{{root}}']);`
 * - Make remote calls via ssh as if they were local.
 */
class SiteProcess extends ProcessBase
{
    /** @var SiteAliasInterface */
    protected $siteAlias;
    /** @var string[] */
    protected $args;
    /** @var string[] */
    protected $options;
    /** @var string[] */
    protected $optionsPassedAsArgs;
    /** @var string */
    protected $cd_remote;
    /** @var TransportInterface */
    protected $transport;

    /**
     * Process arguments and options per the site alias and build the
     * actual command to run.
     */
    public function __construct(SiteAliasInterface $siteAlias, TransportInterface $transport, $args, $options = [], $optionsPassedAsArgs = [])
    {
        $this->siteAlias = $siteAlias;
        $this->transport = $transport;
        $this->args = $args;
        $this->options = $options;
        $this->optionsPassedAsArgs = $optionsPassedAsArgs;

        parent::__construct([]);
    }

    /**
     * Get a starting directory for the remote process.
     *
     * @return string|null
     */
    public function getWorkingDirectory(): ?string
    {
        return $this->cd_remote;
    }

    /**
     * Set a starting directory for the remote process.
     *
     * @param string $cd_remote
     *
     * @return \Consolidation\SiteProcess\SiteProcess
     */
    public function setWorkingDirectory($cd_remote): static
    {
        $this->cd_remote = $cd_remote;
        return $this;
    }

    /**
     * Set a starting directory for the initial/local process.
     *
     * @param string $cd
     *
     * @return \Consolidation\SiteProcess\SiteProcess
     */
    public function setWorkingDirectoryLocal($cd)
    {
        // Symfony 4 REQUIRES that there be a directory set, and defaults
        // it to the cwd if it is not set. We will maintain that pattern here.
        if (!$cd) {
            $cd = getcwd();
        }
        return parent::setWorkingDirectory($cd);
    }

    /**
     * Get the starting directory for the initial/local process.
     *
     * @return string|null;
     */
    public function getWorkingDirectoryLocal()
    {
        return parent::getWorkingDirectory();
    }

    /**
     *
     * @param bool $shouldUseSiteRoot
     * @return $this|\Symfony\Component\Process\Process
     * @throws \Exception
     */
    public function chdirToSiteRoot($shouldUseSiteRoot = true)
    {
        if (!$shouldUseSiteRoot || !$this->siteAlias->hasRoot()) {
            return $this;
        }

        return $this->setWorkingDirectory($this->siteAlias->root());
    }

    /**
     * Take all of our individual arguments and process them for use.
     */
    protected function processArgs()
    {
        $transport = $this->getTransport($this->siteAlias);
        $transport->configure($this);

        $processor = new ArgumentProcessor();
        $selectedArgs = $processor->selectArgs(
            $this->siteAlias,
            $this->args,
            $this->options,
            $this->optionsPassedAsArgs
        );

        // Set environment variables if needed.
        if ($this->siteAlias->has('env-vars')) {
            $selectedArgs = $this->addEnvVars($this->siteAlias->get('env-vars'), $selectedArgs);
        }

        // Ask the transport to drop in a 'cd' if needed.
        if ($this->getWorkingDirectory()) {
            $selectedArgs = $transport->addChdir($this->getWorkingDirectory(), $selectedArgs);
        }

        // Do any necessary interpolation on the selected arguments.
        $processedArgs = $this->interpolate($selectedArgs);

        // Wrap the command with 'ssh' or some other transport if this is
        // a remote command; otherwise, leave it as-is.
        return $transport->wrap($processedArgs);
    }

    /**
     * Wrap the command/args in an env call.
     * @todo Check if this needs to depend on linux/win.
     * @todo Check if this needs to be delegated to transport.
     */
    public function addEnvVars($envVars, $args)
    {
        $envArgs = ['env'];
        foreach ($envVars as $key => $value) {
            $envArgs[] = Escape::forSite($this->siteAlias, $key) . '='
            . Escape::forSite($this->siteAlias, $value);
        }
        return array_merge($envArgs, $args);
    }

    public function setTransport($transport)
    {
        $this->transport = $transport;
    }

    /**
     * Ask the transport manager for the correct transport for the
     * provided alias.
     */
    protected function getTransport(SiteAliasInterface $siteAlias)
    {
        return $this->transport;
    }

    /**
     * @inheritDoc
     */
    public function getCommandLine(): string
    {
        $commandLine = parent::getCommandLine();
        if (empty($commandLine)) {
            $processedArgs = $this->processArgs();
            $commandLine = Escape::argsForSite($this->siteAlias, $processedArgs);
            $commandLine = implode(' ', $commandLine);
            $this->overrideCommandLine($commandLine);
        }
        return $commandLine;
    }

    /**
     * @inheritDoc
     */
    public function start(?callable $callback = null, array $env = []): void
    {
        $cmd = $this->getCommandLine();
        parent::start($callback, $env);
    }

    public function mustRun(?callable $callback = null, array $env = []): static
    {
        if (0 !== $this->run($callback, $env)) {
            // Be less verbose when there is nothing in stdout or stderr.
            if (empty($this->getOutput()) && empty($this->getErrorOutput())) {
                $this->disableOutput();
            }
            throw new ProcessFailedException($this);
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function wait(?callable $callback = null): int
    {
        $return = parent::wait($callback);
        return $return;
    }

    /**
     * interpolate examines each of the arguments in the provided argument list
     * and replaces any token found therein with the value for that key as
     * pulled from the given site alias.
     *
     * Example: "git -C {{root}} status"
     *
     * The token "{{root}}" will be converted to a value via $siteAlias->get('root').
     * The result will replace the token.
     *
     * It is possible to use dot notation in the keys to access nested elements
     * within the site alias record.
     *
     * @param SiteAliasInterface $siteAlias
     * @param type $args
     * @return type
     */
    protected function interpolate($args)
    {
        $interpolator = new Interpolator();
        return array_map(
            function ($arg) use ($interpolator) {
                if ($arg instanceof ShellOperatorInterface) {
                    return $arg;
                }
                return $interpolator->interpolate($this->siteAlias, $arg, false);
            },
            $args
        );
    }

    /**
     * Overrides the command line to be executed.
     *
     * @param string|array $commandline The command to execute
     *
     * @return $this
     *
     * @todo refactor library so this hack to get around changes in
     *   symfony/process 5 is unnecessary.
     */
    private function overrideCommandLine($commandline)
    {
        $commandlineSetter = function ($commandline) {
            $this->commandline = $commandline;
        };
        $commandlineSetter->bindTo($this, Process::class)($commandline);
        return $this;
    }
}
