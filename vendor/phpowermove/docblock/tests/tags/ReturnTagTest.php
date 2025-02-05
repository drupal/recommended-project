<?php declare(strict_types=1);

namespace phpowermove\docblock\tests\tags;

use phpowermove\docblock\tags\ReturnTag;
use PHPUnit\Framework\TestCase;

class ReturnTagTest extends TestCase {
	public function testReadWrite() {
		$return = new ReturnTag('Foo bar');
		$this->assertEquals('@return Foo bar', $return->toString());
	}
}
