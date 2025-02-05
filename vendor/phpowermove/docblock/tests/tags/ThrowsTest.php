<?php declare(strict_types=1);

namespace phpowermove\docblock\tests\tags;

use phpowermove\docblock\tags\ThrowsTag;
use PHPUnit\Framework\TestCase;

class ThrowsTagTest extends TestCase {
	public function testReadWrite() {
		$ex = new ThrowsTag('\Exception oups');
		$this->assertEquals('@throws \Exception oups', $ex->toString());
	}
}
