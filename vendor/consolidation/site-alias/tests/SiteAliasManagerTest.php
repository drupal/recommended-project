<?php
namespace Consolidation\SiteAlias;

use Consolidation\SiteAlias\Util\YamlDataFileLoader;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Yaml\Yaml;

class SiteAliasManagerTest extends TestCase
{
    use FixtureFactory;

    protected $manager;

    /**
     * Set up for tests
     */
    public function setUp(): void
    {
        $root = $this->siteDir();
        $referenceData = [];
        $siteAliasFixtures = $this->fixturesDir() . '/sitealiases/sites';

        $aliasLoader = new SiteAliasFileLoader();
        $ymlLoader = new YamlDataFileLoader();
        $aliasLoader->addLoader('yml', $ymlLoader);

        $this->manager = new SiteAliasManager($aliasLoader, $root);
        $this->manager
            ->setReferenceData($referenceData)
            ->addSearchLocation($siteAliasFixtures);
    }

    public function managerGetTestValues()
    {
        return [
            [
                '@single.other', false,
            ],

            [
                '@other.single.other', false,
            ],

            [
                '@single.dev', 'foo: bar
root: /path/to/single',
            ],

        ];
    }

    public function managerGetWithOtherLocationTestValues()
    {
        return [
            [
                '@single.other', false,
            ],

            [
                '@other.single.other', 'foo: baz
root: /other/other/path/to/single',
            ],

            [
                '@single.dev', 'foo: bar
root: /path/to/single',
            ],

        ];
    }

    public function managerGetWithDupLocationsTestValues()
    {
        return [
            [
                '@single.dev', 'foo: bar
root: /path/to/single',
            ],

            [
                '@other.single.dev', 'foo: baz
root: /other/path/to/single',
            ],

            [
                '@dup.single.dev', 'foo: dup
root: /dup/path/to/single',
            ],

        ];
    }

    /**
     * This test is just to ensure that our fixture data is being loaded
     * accurately so that we can start writing tests. Its okay to remove
     * rather than maintain this test once the suite is mature.
     */
    public function testGetMultiple()
    {
        // First set of tests: get all aliases in the default location

        $all = $this->manager->getMultiple();
        $allNames = array_keys($all);
        sort($allNames);

        $this->assertEquals('@single.alternate,@single.dev,@single.empty,@wild.*,@wild.dev', implode(',', $allNames));

        $all = $this->manager->getMultiple('@single');
        $allNames = array_keys($all);
        sort($allNames);

        $this->assertEquals('@single.alternate,@single.dev,@single.empty', implode(',', $allNames));

        // Next set of tests: Get all aliases in the 'other' location

        $this->addAlternateLocation('other');

        $all = $this->manager->getMultiple();
        $allNames = array_keys($all);
        sort($allNames);

        $this->assertEquals('@other.bob.dev,@other.bob.other,@other.fred.dev,@other.fred.other,@other.single.dev,@other.single.other,@single.alternate,@single.dev,@single.empty,@wild.*,@wild.dev', implode(',', $allNames));

        $all = $this->manager->getMultiple('@other');
        $allNames = array_keys($all);
        sort($allNames);

        $this->assertEquals('@other.bob.dev,@other.bob.other,@other.fred.dev,@other.fred.other,@other.single.dev,@other.single.other', implode(',', $allNames));

        // Add the 'dup' location and do some more tests

        $this->addAlternateLocation('dup');

        $all = $this->manager->getMultiple();
        $allNames = array_keys($all);
        sort($allNames);

        $this->assertEquals('@dup.bob.dev,@dup.bob.other,@dup.fred.dev,@dup.fred.other,@dup.single.alternate,@dup.single.dev,@other.bob.dev,@other.bob.other,@other.fred.dev,@other.fred.other,@other.single.dev,@other.single.other,@single.alternate,@single.dev,@single.empty,@wild.*,@wild.dev', implode(',', $allNames));

        $all = $this->manager->getMultiple('@dup');
        $allNames = array_keys($all);
        sort($allNames);

        $this->assertEquals('@dup.bob.dev,@dup.bob.other,@dup.fred.dev,@dup.fred.other,@dup.single.alternate,@dup.single.dev', implode(',', $allNames));

        $all = $this->manager->getMultiple('@other');
        $allNames = array_keys($all);
        sort($allNames);

        $this->assertEquals('@other.bob.dev,@other.bob.other,@other.fred.dev,@other.fred.other,@other.single.dev,@other.single.other', implode(',', $allNames));

        $all = $this->manager->getMultiple('@dup.single');
        $allNames = array_keys($all);
        sort($allNames);

        $this->assertEquals('@dup.single.alternate,@dup.single.dev', implode(',', $allNames));

        $all = $this->manager->getMultiple('@other.single');
        $allNames = array_keys($all);
        sort($allNames);

        $this->assertEquals('@other.single.dev,@other.single.other', implode(',', $allNames));
    }

    /**
     * @covers \Consolidation\SiteAlias\SiteAlias::root()
     */
    public function testGetRoot() {
        /* @var SiteAlias $alias */
        $alias = $this->manager->get('@single');
        $this->assertEquals($alias->root(), '/path/to/single');
        /* @var SiteAlias $alias */
        $alias = $this->manager->get('@single.common');
        // Ensure that when root is not specified in the alias, an Exception is
        // thrown.
        $this->expectExceptionMessage('Site alias @single.common does not specify a root.');
        $alias->root();
    }

    /**
     * @dataProvider managerGetTestValues
     */
    public function testGet(
        $aliasName,
        $expected)
    {
        $alias = $this->manager->get($aliasName);
        $actual = $this->renderAlias($alias);
        $this->assertEquals(str_replace("\r", '', $expected), $actual);
    }

    /**
     * @dataProvider managerGetWithOtherLocationTestValues
     */
    public function testGetWithOtherLocation(
        $aliasName,
        $expected)
    {
        $this->addAlternateLocation('other');
        $this->testGet($aliasName, $expected);
    }

    /**
     * @dataProvider managerGetWithDupLocationsTestValues
     */
    public function testGetWithDupLocations(
        $aliasName,
        $expected)
    {
        $this->addAlternateLocation('dup');
        $this->testGetWithOtherLocation($aliasName, $expected);
    }

    protected function addAlternateLocation($fixtureDirName)
    {
        // Add another search location IN ADDITION to the one
        // already added in the setup() mehtod.
        $siteAliasFixtures = $this->fixturesDir() . '/sitealiases/' . $fixtureDirName;
        $this->manager->addSearchLocation($siteAliasFixtures);
    }

    protected function renderAlias($alias)
    {
        if (!$alias) {
            return false;
        }

        return trim(Yaml::Dump($alias->export()));
    }
}
