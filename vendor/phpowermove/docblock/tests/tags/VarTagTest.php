<?php declare(strict_types=1);

namespace phpowermove\docblock\tests\tags;

use phpowermove\docblock\Docblock;
use phpowermove\docblock\tags\VarTag;
use PHPUnit\Framework\TestCase;

class VarTagTest extends TestCase {
	public function testReadWrite(): void {
		$var = new VarTag('Foo ...$bar');
		$this->assertEquals('@var Foo ...$bar', $var->toString());
	}

	public function testDocblock(): void {
		$expected = '/**
 * @var mixed $foo bar
 */';
		$docblock = new Docblock();
		$var = VarTag::create()
			->setType('mixed')
			->setVariable('foo')
			->setDescription('bar')
		;
		$docblock->appendTag($var);

		$this->assertEquals($expected, $docblock->toString());
	}
}
