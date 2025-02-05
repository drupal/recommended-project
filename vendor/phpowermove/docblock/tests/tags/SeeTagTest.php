<?php declare(strict_types=1);

namespace phpowermove\docblock\tests\tags;

use phpowermove\docblock\tags\SeeTag;
use PHPUnit\Framework\TestCase;

class SeeTagTest extends TestCase {
	public function testReadWrite(): void {
		$see = new SeeTag('Dest::nation() 0815');
		$this->assertEquals('@see Dest::nation() 0815', $see->toString());
	}

	public function testReference(): void {
		$see = new SeeTag();
		$this->assertSame($see, $see->setReference('hier-lang'));
		$this->assertEquals('hier-lang', $see->getReference());
	}
}
