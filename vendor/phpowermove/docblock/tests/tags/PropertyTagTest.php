<?php declare(strict_types=1);

namespace phpowermove\docblock\tests\tags;

use phpowermove\docblock\tags\PropertyTag;
use PHPUnit\Framework\TestCase;

class PropertyTagTest extends TestCase {
	public function testReadWrite() {
		$prop = new PropertyTag('string $var');
		$this->assertEquals('@property string $var', $prop->toString());
	}
}
