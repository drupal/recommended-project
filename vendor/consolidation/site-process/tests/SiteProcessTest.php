<?php

namespace Consolidation\SiteProcess;

use PHPUnit\Framework\TestCase;
use Consolidation\SiteProcess\Util\ArgumentProcessor;
use Consolidation\SiteProcess\Util\Escape;
use Consolidation\SiteAlias\SiteAlias;

class SiteProcessTest extends TestCase
{
    /**
     * Data provider for testSiteProcess.
     */
    public function siteProcessTestValues()
    {
        return [
            [
                "ls -al",
                false,
                false,
                [],
                ['ls', '-al'],
                [],
                [],
                NULL,
            ],

            [
                "ls -al",
                'src',
                false,
                [],
                ['ls', '-al'],
                [],
                [],
                NULL,
            ],

            [
                "ls -al /path1 /path2",
                false,
                false,
                [],
                ['ls', '-al', '/path1', '/path2'],
                [],
                [],
                NULL,
            ],

            [
                "ssh -o PasswordAuthentication=no www-admin@server.net 'ls -al'",
                false,
                false,
                ['host' => 'server.net', 'user' => 'www-admin'],
                ['ls', '-al'],
                [],
                [],
                NULL,
            ],

            [
                "ssh -o PasswordAuthentication=no www-admin@server.net 'cd /srv/www/docroot && ls -al'",
                false,
                false,
                ['host' => 'server.net', 'user' => 'www-admin', 'root' => '/srv/www/docroot'],
                ['ls', '-al'],
                [],
                [],
                NULL,
            ],

            [
                "ssh -o PasswordAuthentication=no www-admin@server.net 'cd src && ls -al'",
                'src',
                false,
                ['host' => 'server.net', 'user' => 'www-admin'],
                ['ls', '-al'],
                [],
                [],
                NULL,
            ],

            [
                "ssh -t -o PasswordAuthentication=no www-admin@server.net 'ls -al'",
                false,
                true,
                ['host' => 'server.net', 'user' => 'www-admin'],
                ['ls', '-al'],
                [],
                [],
                NULL,
            ],

            [
                "ssh -t -o PasswordAuthentication=no www-admin@server.net 'cd src && ls -al /path1 /path2'",
                'src',
                true,
                ['host' => 'server.net', 'user' => 'www-admin'],
                ['ls', '-al', '/path1', '/path2'],
                [],
                [],
                NULL,
            ],

            [
                "ssh -t -o PasswordAuthentication=no www-admin@server.net 'cd src && ls -al /path1 /path2'",
                'src',
                true,
                ['host' => 'server.net', 'user' => 'www-admin'],
                ['ls', '-al', '/path1', '/path2'],
                [],
                [],
                NULL,
            ],

            [
                "docker-compose exec --workdir src --user root drupal ls -al /path1 /path2",
                'src',
                true,
                ['docker' => ['service' => 'drupal', 'exec' => ['options' => '--user root']]],
                ['ls', '-al', '/path1', '/path2'],
                [],
                [],
                NULL,
            ],

            [
                "docker-compose -p project exec --workdir src --user root drupal ls -al /path1 /path2",
                'src',
                true,
                ['docker' => ['service' => 'drupal', 'project' => 'project', 'exec' => ['options' => '--user root']]],
                ['ls', '-al', '/path1', '/path2'],
                [],
                [],
                NULL,
            ],

            [
                "drush status '--fields=root,uri'",
                false,
                false,
                [],
                ['drush', 'status'],
                ['fields' => 'root,uri'],
                [],
                'LINUX',
            ],

            [
                'drush status --fields=root,uri',
                  false,
                  false,
                  [],
                  ['drush', 'status'],
                  ['fields' => 'root,uri'],
                  [],
                  'WIN',
            ],

            [
                "drush rsync a b -- --exclude=vendor",
                false,
                false,
                [],
                ['drush', 'rsync', 'a', 'b',],
                [],
                ['exclude' => 'vendor'],
                NULL,
            ],

            [
                "drush rsync a b -- --exclude=vendor --include=vendor/autoload.php",
                false,
                false,
                [],
                ['drush', 'rsync', 'a', 'b', '--', '--include=vendor/autoload.php'],
                [],
                ['exclude' => 'vendor'],
                NULL,
            ],

            [
                "env foo=bar baz=zong drush status",
                false,
                false,
                ['env-vars' => ['foo' => 'bar', 'baz' => 'zong']],
                ['drush', 'status'],
                [],
                [],
                NULL,
            ],
        ];
    }

    /**
     * Test the SiteProcess class.
     *
     * @dataProvider siteProcessTestValues
     */
    public function testSiteProcess(
        $expected,
        $cd,
        $useTty,
        $siteAliasData,
        $args,
        $options,
        $optionsPassedAsArgs,
        $os)
    {
        if (Escape::isWindows() != Escape::isWindows($os)) {
          $this->markTestSkipped("OS isn't supported");
        }
        if ($useTty && Escape::isWindows($os)) {
          $this->markTestSkipped('Windows doesn\'t have /dev/tty support');
        }
        // Symfony 7 won't let us use setTty when there isn't an actual tty,
        // whereas Symfony 6 is more forgiving. (Our test doesn't actually need
        // the tty.) Since I don't know a good way to detect Symfony 7+, I instead
        // allow the test to run on PHP < 8.2.0, since Symfony 7 requires PHP 8.2+.
        if ($useTty && getenv('CI') && (version_compare("8.2.0", PHP_VERSION) <= 0)) {
          $this->markTestSkipped('CI doesn\'t provide /dev/tty support');
        }
        $processManager = ProcessManager::createDefault();
        $siteAlias = new SiteAlias($siteAliasData, '@alias.dev');
        $siteProcess = $processManager->siteProcess($siteAlias, $args, $options, $optionsPassedAsArgs);
        $siteProcess->setTty($useTty);
        // The transport handles the chdir during processArgs().
        $fallback = $siteAlias->hasRoot() ? $siteAlias->root() : null;
        $siteProcess->setWorkingDirectory($cd ?: $fallback);

        $actual = $siteProcess->getCommandLine();
        $this->assertEquals($expected, $actual);
    }

    /**
     * Data provider for testSiteProcessJson.
     */
    public function siteProcessJsonTestValues()
    {
        return [
            [
                'Output is empty.',
                '',
                'LINUX',
            ],
            [
                "Unable to decode output into JSON: Syntax error\n\nNo json data here",
                'No json data here',
                NULL,
            ],
            [
                '{"foo":"bar"}',
                '{"foo":"bar"}',
                NULL,
            ],
            [
                '{"foo":"b\'ar"}',
                '{"foo":"b\'ar"}',
                NULL,
            ],
            [
                '{"foo":"bar"}',
                'Ignored leading data {"foo":"bar"} Ignored trailing data',
                NULL,
            ],
            [
                '["a","b","c"]',
                '["a", "b", "c"]',
                NULL,
            ],
            [
                '"string"',
                '"string"',
                NULL,
            ],
            [
                '[]',
                '[]',
                NULL,
            ],
        ];
    }

    /**
     * Test the SiteProcess class.
     *
     * @dataProvider siteProcessJsonTestValues
     */
    public function testSiteProcessJson(
        $expected,
        $data,
        $os)
    {
        if (Escape::isWindows()) {
          $this->markTestSkipped("Windows is not working yet. PRs welcome.");
        }
        $args = ['echo', $data];
        $processManager = ProcessManager::createDefault();
        $siteAlias = new SiteAlias([], '@alias.dev');
        $siteAlias->set('os', $os);
        $siteProcess = $processManager->siteProcess($siteAlias, $args);
        $siteProcess->mustRun();

        try {
            $actual = $siteProcess->getOutputAsJson();
            $actual = json_encode($actual, true);
        }
        catch (\Exception $e) {
            $actual = $e->getMessage();
        }
        $this->assertEquals($expected, $actual);
    }
}
