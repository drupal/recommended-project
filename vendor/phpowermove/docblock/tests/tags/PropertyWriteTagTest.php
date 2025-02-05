<?php declare(strict_types=1);

namespace phpowermove\docblock\tests\tags;

use phpowermove\docblock\tags\PropertyWriteTag;
use PHPUnit\Framework\TestCase;

class PropertyWriteTagTest extends TestCase {
	public function testReadWrite() {
		$prop = new PropertyWriteTag('string $var');
		$this->assertEquals('@property-write string $var', $prop->toString());
	}
}
