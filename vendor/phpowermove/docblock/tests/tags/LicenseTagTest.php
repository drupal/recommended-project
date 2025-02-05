<?php declare(strict_types=1);

namespace phpowermove\docblock\tests\tags;

use phpowermove\docblock\tags\LicenseTag;
use PHPUnit\Framework\TestCase;

class LicenseTagTest extends TestCase {
	public function testReadWrite(): void {
		$license = new LicenseTag('MIT');

		$this->assertEquals('MIT', $license->getLicense());
		$this->assertEquals('@license MIT', $license->toString());

		$license = new LicenseTag('http://opensource.org/licenses/MIT MIT');

		$this->assertEquals('MIT', $license->getLicense());
		$this->assertEquals('http://opensource.org/licenses/MIT', $license->getUrl());
		$this->assertEquals('@license http://opensource.org/licenses/MIT MIT', $license->toString());
	}

	public function testLicense(): void {
		$name = 'gossi';
		$license = new LicenseTag();

		$this->assertSame($license, $license->setLicense($name));
		$this->assertEquals($name, $license->getLicense());
	}

	public function testUrl(): void {
		$url = 'http://opensource.org/licenses/MIT';
		$license = new LicenseTag();

		$this->assertSame($license, $license->setUrl($url));
		$this->assertEquals($url, $license->getUrl());
	}
}
