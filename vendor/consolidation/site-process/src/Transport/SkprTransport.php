<?php

namespace Consolidation\SiteProcess\Transport;

use Consolidation\SiteProcess\SiteProcess;
use Consolidation\SiteAlias\SiteAliasInterface;
use Consolidation\SiteProcess\Util\Shell;

/**
 * SkprTransport knows how to wrap a command to run on a site hosted
 * on the Skpr platform.
 */
class SkprTransport implements TransportInterface
{

    /** @var \Consolidation\SiteAlias\SiteAliasInterface */
    protected $siteAlias;

    public function __construct(SiteAliasInterface $siteAlias)
    {
        $this->siteAlias = $siteAlias;
    }

    /**
     * @inheritdoc
     */
    public function configure(SiteProcess $process)
    {
        $path = $this->siteAlias->getDefault('skpr.path', getcwd());
        if ($path) {
            $process->chdirToSiteRoot($path);
        }
    }

    /**
     * inheritdoc
     */
    public function wrap($args)
    {
        $environment = $this->siteAlias->get('skpr.env');

        $transport = [
            'skpr',
            'exec',
            "$environment",
        ];
        $transport[] = "--";

        return array_merge($transport, $args);
    }

    /**
     * @inheritdoc
     */
    public function addChdir($cd_remote, $args)
    {
        return array_merge(
            [
                'cd',
                $cd_remote,
                Shell::op('&&'),
            ],
            $args
        );
    }
}
