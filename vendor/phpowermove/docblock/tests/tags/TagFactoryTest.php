<?php declare(strict_types=1);

namespace phpowermove\docblock\tests\tags;

use phpowermove\docblock\tags\AuthorTag;
use phpowermove\docblock\tags\TagFactory;
use phpowermove\docblock\tags\UnknownTag;
use PHPUnit\Framework\TestCase;

class TagFactoryTest extends TestCase {
	public function testFactory(): void {
		$factory = new TagFactory();

		$author = $factory->create('author', 'lil-g');
		$this->assertTrue($author instanceof AuthorTag);
		$this->assertEquals('lil-g', $author->getName());

		$unknown = $factory->create('wurst');
		$this->assertTrue($unknown instanceof UnknownTag);
		$this->assertEquals('wurst', $unknown->getTagName());
	}
}
