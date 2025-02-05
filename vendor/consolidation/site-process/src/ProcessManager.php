<?php

namespace Consolidation\SiteProcess;

use Consolidation\SiteProcess\Factory\KubectlTransportFactory;
use Consolidation\SiteProcess\Factory\SkprTransportFactory;
use Consolidation\SiteProcess\Factory\VagrantTransportFactory;
use Psr\Log\LoggerInterface;
use Consolidation\SiteAlias\SiteAliasInterface;
use Consolidation\SiteProcess\Factory\SshTransportFactory;
use Consolidation\SiteProcess\Factory\DockerComposeTransportFactory;
use Consolidation\SiteProcess\Factory\TransportFactoryInterface;
use Consolidation\SiteProcess\Transport\LocalTransport;
use Symfony\Component\Process\Process;
use Consolidation\Config\Config;
use Consolidation\Config\ConfigInterface;
use Consolidation\Config\ConfigAwareInterface;
use Consolidation\Config\ConfigAwareTrait;
use Consolidation\SiteAlias\SiteAliasWithConfig;

/**
 * ProcessManager will create a SiteProcess to run a command on a given
 * site as indicated by a SiteAlias.
 *
 * ProcessManager also manages a collection of transport factories, and
 * will produce transport instances as needed for provided site aliases.
 */
class ProcessManager implements ConfigAwareInterface
{
    use ConfigAwareTrait;

    /** @var ConfigInterface */
    protected $configRuntime;

    protected $transportFactories = [];

    public function __construct()
    {
        $this->config = new Config();
        $this->configRuntime = new Config();
    }

    /**
     * setConfigRuntime sets the config object that holds runtime config
     * items, i.e. config set from the commandline rather than a config file.
     * Configuration priority (highest to lowest) is:
     *   - config runtime
     *   - site alias
     *   - config files
     */
    public function setConfigRuntime(ConfigInterface $configRuntime)
    {
        $this->configRuntime = $configRuntime;
        return $this;
    }

    /**
     * createDefault creates a Transport manager and add the default transports to it.
     */
    public static function createDefault()
    {
        $processManager = new self();
        return static::addTransports($processManager);
    }

    /**
     * addTransports adds the avaiable transports to the
     * provided process manager.
     */
    public static function addTransports(ProcessManager $processManager)
    {
        $processManager->add(new SshTransportFactory());
        $processManager->add(new KubectlTransportFactory());
        $processManager->add(new SkprTransportFactory());
        $processManager->add(new DockerComposeTransportFactory());
        $processManager->add(new VagrantTransportFactory());

        return $processManager;
    }

    /**
     * Return a site process configured with an appropriate transport
     *
     * @param SiteAliasInterface $siteAlias Target for command
     * @param array $args Command arguments
     * @param array $options Associative array of command options
     * @param array $optionsPassedAsArgs Associtive array of options to be passed as arguments (after double-dash)
     * @return Process
     */
    public function siteProcess(SiteAliasInterface $siteAlias, $args = [], $options = [], $optionsPassedAsArgs = [])
    {
        $transport = $this->getTransport($siteAlias);
        $process = new SiteProcess($siteAlias, $transport, $args, $options, $optionsPassedAsArgs);
        return $process;
    }

    /**
     * Create a Process instance from a commandline string.
     * @param array $command The command to run and its arguments listed as separate entries
     * @param string|null $cwd     The working directory or null to use the working dir of the current PHP process
     * @param array|null $env     The environment variables or null to use the same environment as the current PHP process
     * @param mixed|null $input   The input as stream resource, scalar or \Traversable, or null for no input
     * @param int|float|null $timeout The timeout in seconds or null to disable
     * @return Process
     */
    public function process($command, $cwd = null, ?array $env = null, $input = null, $timeout = 60)
    {
        return new ProcessBase($command, $cwd, $env, $input, $timeout);
    }

    /**
     * Create a Process instance from a commandline string.
     * @param string $command The commandline string to run
     * @param string|null $cwd     The working directory or null to use the working dir of the current PHP process
     * @param array|null $env     The environment variables or null to use the same environment as the current PHP process
     * @param mixed|null $input   The input as stream resource, scalar or \Traversable, or null for no input
     * @param int|float|null $timeout The timeout in seconds or null to disable
     * @return Process
     */
    public function shell($command, $cwd = null, ?array $env = null, $input = null, $timeout = 60)
    {
        return ProcessBase::fromShellCommandline($command, $cwd, $env, $input, $timeout);
    }

    /**
     * add a transport factory to our factory list
     * @param TransportFactoryInterface $factory
     */
    public function add(TransportFactoryInterface $factory)
    {
        $this->transportFactories[] = $factory;
        return $this;
    }

    /**
     * hasTransport determines if there is a transport that handles the
     * provided site alias.
     *
     * @param SiteAliasInterface $siteAlias
     * @return boolean
     */
    public function hasTransport(SiteAliasInterface $siteAlias)
    {
        return $this->getTransportFactory($siteAlias) !== null;
    }

    /**
     * getTransport returns a transport that is applicable to the provided site alias.
     *
     * @param SiteAliasInterface $siteAlias
     * @return TransportInterface
     */
    public function getTransport(SiteAliasInterface $siteAlias)
    {
        $factory = $this->getTransportFactory($siteAlias);

        $siteAliasWithConfig = SiteAliasWithConfig::create($siteAlias, $this->config, $this->configRuntime);

        if ($factory) {
            return $factory->create($siteAliasWithConfig);
        }
        return new LocalTransport();
    }

    /**
     * getTransportFactory returns a factory for the provided site alias.
     *
     * @param SiteAliasInterface $siteAlias
     * @return TransportFactoryInterface
     */
    protected function getTransportFactory(SiteAliasInterface $siteAlias)
    {
        foreach ($this->transportFactories as $factory) {
            if ($factory->check($siteAlias)) {
                return $factory;
            }
        }
        return null;
    }
}
