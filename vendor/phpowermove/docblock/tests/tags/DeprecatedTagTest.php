<?php declare(strict_types=1);

namespace phpowermove\docblock\tests\tags;

use phpowermove\docblock\tags\DeprecatedTag;
use PHPUnit\Framework\TestCase;

class DeprecatedTagTest extends TestCase {
	public function testReadWrite(): void {
		$deprecated = new DeprecatedTag();
		$this->assertEquals('@deprecated', $deprecated->toString());
	}
}
