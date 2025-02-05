<?php
namespace Consolidation\SiteAlias;

use PHPUnit\Framework\TestCase;

class SiteAliasNameTest extends TestCase
{
    public function testSiteAliasName()
    {
        // Test an ambiguous sitename or env alias.
        $name = SiteAliasName::parse('@simple');
        $this->assertFalse($name->hasLocation());
        $this->assertTrue(!$name->hasSitename());
        $this->assertTrue($name->hasEnv());
        $this->assertEquals('simple', $name->env());
        $this->assertEquals('@self.simple', (string)$name);

        // Test a non-ambiguous sitename.env alias.
        $name = SiteAliasName::parse('@site.env');
        $this->assertFalse($name->hasLocation());
        $this->assertTrue($name->hasSitename());
        $this->assertTrue($name->hasEnv());
        $this->assertEquals('site', $name->sitename());
        $this->assertEquals('env', $name->env());
        $this->assertEquals('@site.env', (string)$name);

        // Test a non-ambiguous location.sitename.env alias.
        $name = SiteAliasName::parse('@location.site.env');
        $this->assertTrue($name->hasLocation());
        $this->assertTrue($name->hasSitename());
        $this->assertTrue($name->hasEnv());
        $this->assertEquals('location', $name->location());
        $this->assertEquals('site', $name->sitename());
        $this->assertEquals('env', $name->env());
        $this->assertEquals('@location.site.env', (string)$name);

        // Test an invalid alias - bad character
        $name = SiteAliasName::parse('!site.env');
        $this->assertFalse($name->hasLocation());
        $this->assertFalse($name->hasSitename());
        $this->assertFalse($name->hasEnv());

        // Test an invalid alias - too many separators
        $name = SiteAliasName::parse('@location.group.site.env');
        $this->assertFalse($name->hasLocation());
        $this->assertFalse($name->hasSitename());
        $this->assertFalse($name->hasEnv());
    }
}
