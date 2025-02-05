<?php declare(strict_types=1);

namespace phpowermove\docblock\tests\tags;

use phpowermove\docblock\tags\MethodTag;
use PHPUnit\Framework\TestCase;

class MethodTagTest extends TestCase {
	public function testReadWrite(): void {
		$method = new MethodTag('string myMethod()');
		$this->assertEquals('@method string myMethod()', $method->toString());
	}
}
