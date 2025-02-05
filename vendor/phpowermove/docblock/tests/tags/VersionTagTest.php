<?php declare(strict_types=1);

namespace phpowermove\docblock\tests\tags;

use phpowermove\docblock\tags\VersionTag;
use PHPUnit\Framework\TestCase;

class VersionTagTest extends TestCase {
	public function testReadWrite(): void {
		$version = new VersionTag('1.3.3.7 jupjup');
		$this->assertEquals('@version 1.3.3.7 jupjup', $version->toString());
		$this->assertEquals('jupjup', $version->getDescription());
		$this->assertEquals('1.3.3.7', $version->getVersion());
		$this->assertSame($version, $version->setVersion('3.14'));
		$this->assertEquals('3.14', $version->getVersion());
	}
}
