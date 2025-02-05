<?php
namespace Consolidation\SiteAlias;

use PHPUnit\Framework\TestCase;
use Consolidation\SiteAlias\Util\YamlDataFileLoader;

class SiteAliasFileLoaderTest extends TestCase
{
    use FixtureFactory;
    use FunctionUtils;

    function setUp(): void
    {
        $this->sut = new SiteAliasFileLoader();

        $ymlLoader = new YamlDataFileLoader();
        $this->sut->addLoader('yml', $ymlLoader);
    }

    public function testLoadWildAliasFile()
    {
        $siteAliasFixtures = $this->fixturesDir() . '/sitealiases/sites';
        $this->assertTrue(is_dir($siteAliasFixtures));
        $this->assertTrue(is_file($siteAliasFixtures . '/wild.site.yml'));

        $this->sut->addSearchLocation($siteAliasFixtures);

        // Try to get the dev environment.
        $name = SiteAliasName::parse('@wild.dev');
        $result = $this->callProtected('loadSingleAliasFile', [$name]);
        $this->assertTrue($result instanceof SiteAlias);
        $this->assertEquals('/path/to/wild', $result->get('root'));
        $this->assertEquals('bar', $result->get('foo'));

        // Try to fetch an environment that does not exist. Since this is
        // a wildcard alias, there should
        $name = SiteAliasName::parse('@wild.other');
        $result = $this->callProtected('loadSingleAliasFile', [$name]);
        $this->assertTrue($result instanceof SiteAlias);
        $this->assertEquals('/wild/path/to/wild', $result->get('root'));
        $this->assertEquals('bar', $result->get('foo'));

    }

    public function testLoadSingleAliasFile()
    {
        $siteAliasFixtures = $this->fixturesDir() . '/sitealiases/sites';
        $this->assertTrue(is_dir($siteAliasFixtures));
        $this->assertTrue(is_file($siteAliasFixtures . '/simple.site.yml'));
        $this->assertTrue(is_file($siteAliasFixtures . '/single.site.yml'));

        $this->sut->addSearchLocation($siteAliasFixtures);

        // Add a secondary location
        $siteAliasFixtures = $this->fixturesDir() . '/sitealiases/other';
        $this->assertTrue(is_dir($siteAliasFixtures));
        $this->sut->addSearchLocation($siteAliasFixtures);

        // Look for a simple alias with no environments defined
        $name = new SiteAliasName('simple');
        $this->assertEquals('simple', $name->sitename());
        $result = $this->callProtected('loadSingleAliasFile', [$name]);
        $this->assertTrue($result instanceof SiteAlias);
        $this->assertEquals('/path/to/simple', $result->get('root'));

        // Look for a single alias without an environment specified.
        $name = new SiteAliasName('single');
        $this->assertEquals('single', $name->sitename());
        $result = $this->callProtected('loadSingleAliasFile', [$name]);
        $this->assertTrue($result instanceof SiteAlias);
        $this->assertEquals('/path/to/single', $result->get('root'));
        $this->assertEquals('bar', $result->get('foo'));

        // Same test, but with environment explicitly requested.
        $name = SiteAliasName::parse('@single.alternate');
        $result = $this->callProtected('loadSingleAliasFile', [$name]);
        $this->assertTrue($result instanceof SiteAlias);
        $this->assertEquals('/alternate/path/to/single', $result->get('root'));
        $this->assertEquals('bar', $result->get('foo'));

        // Same test, but with location explicitly filtered.
        $name = SiteAliasName::parse('@other.single.dev');
        $result = $this->callProtected('loadSingleAliasFile', [$name]);
        $this->assertTrue($result instanceof SiteAlias);
        $this->assertEquals('/other/path/to/single', $result->get('root'));
        $this->assertEquals('baz', $result->get('foo'));

        // Try to fetch an alias that does not exist.
        $name = SiteAliasName::parse('@missing');
        $result = $this->callProtected('loadSingleAliasFile', [$name]);
        $this->assertFalse($result);

        // Try to fetch an alias using a missing location
        $name = SiteAliasName::parse('@missing.single.alternate');
        $result = $this->callProtected('loadSingleAliasFile', [$name]);
        $this->assertFalse($result);
    }

    public function testLoad()
    {
        $this->sut->addSearchLocation($this->fixturesDir() . '/sitealiases/sites');

        // Look for a simple alias with no environments defined
        $name = new SiteAliasName('simple');
        $result = $this->sut->load($name);
        $this->assertTrue($result instanceof SiteAlias);
        $this->assertEquals('/path/to/simple', $result->get('root'));

        // Look for a single alias without an environment specified.
        $name = new SiteAliasName('single');
        $result = $this->sut->load($name);
        $this->assertTrue($result instanceof SiteAlias);
        $this->assertEquals('/path/to/single', $result->get('root'));
        $this->assertEquals('bar', $result->get('foo'));

        // Same test, but with environment explicitly requested.
        $name = new SiteAliasName('single', 'alternate');
        $result = $this->sut->load($name);
        $this->assertTrue($result instanceof SiteAlias);
        $this->assertEquals('/alternate/path/to/single', $result->get('root'));
        $this->assertEquals('bar', $result->get('foo'));

        // Try to fetch an alias that does not exist.
        $name = new SiteAliasName('missing');
        $result = $this->sut->load($name);
        $this->assertFalse($result);

        // Try to fetch an alias that does not exist.
        $name = new SiteAliasName('missing');
        $result = $this->sut->load($name);
        $this->assertFalse($result);
    }

    public function testLoadAll()
    {
        $this->sut->addSearchLocation($this->fixturesDir() . '/sitealiases/sites');
        $this->sut->addSearchLocation($this->fixturesDir() . '/sitealiases/other');

        $all = $this->sut->loadAll();
        $actualKeys = array_keys($all);
        sort($all);
        $this->assertEquals('@other.bob.dev,@other.bob.other,@other.fred.dev,@other.fred.other,@other.single.dev,@other.single.other,@single.alternate,@single.dev,@single.empty,@wild.*,@wild.dev', implode(',', $actualKeys));
    }

    public function testLoadMultiple()
    {
        $this->sut->addSearchLocation($this->fixturesDir() . '/sitealiases/sites');
        $this->sut->addSearchLocation($this->fixturesDir() . '/sitealiases/other');

        $aliases = $this->sut->loadMultiple('single');
        $this->assertEquals('@single.dev,@single.alternate,@single.empty,@other.single.dev,@other.single.other', implode(',', array_keys($aliases)));
    }

    public function testLoadLocation()
    {
        $this->sut->addSearchLocation($this->fixturesDir() . '/sitealiases/sites');
        $this->sut->addSearchLocation($this->fixturesDir() . '/sitealiases/other');

        $aliases = $this->sut->loadLocation('other');
        $actualKeys = array_keys($aliases);
        sort($actualKeys);
        $this->assertEquals('@other.bob.dev,@other.bob.other,@other.fred.dev,@other.fred.other,@other.single.dev,@other.single.other', implode(',', $actualKeys));
    }

    public function testLoadOverrideSelf()
    {
        $this->sut->setRoot($this->fixturesDir() . '/sitealiases/self-override');
        $this->sut->addSearchLocation($this->fixturesDir() . '/sitealiases/self-override/drush/sites');

        // Specified site alias data should take precedence of @self data.
        $name = new SiteAliasName('foo', 'prod');
        $result = $this->sut->load($name);
        $this->assertTrue($result instanceof SiteAlias);
        $this->assertEquals('overridden', $result->get('bar'));
    }
}
