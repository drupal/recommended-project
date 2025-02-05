<?php declare(strict_types=1);

namespace phpowermove\docblock\tests\tags;

use phpowermove\docblock\tags\AuthorTag;
use PHPUnit\Framework\TestCase;

class AuthorTagTest extends TestCase {
	public function testReadWrite(): void {
		$author = new AuthorTag('gossi <hans@wurst.de>');

		$this->assertEquals('gossi', $author->getName());
		$this->assertEquals('hans@wurst.de', $author->getEmail());
		$this->assertEquals('@author gossi <hans@wurst.de>', $author->toString());

		$author = new AuthorTag('lil-g');

		$this->assertEquals('lil-g', $author->getName());
		$this->assertEmpty($author->getEmail());
		$this->assertEquals('@author lil-g', $author->toString());
	}

	public function testName(): void {
		$name = 'gossi';
		$author = new AuthorTag();

		$this->assertSame($author, $author->setName($name));
		$this->assertEquals($name, $author->getName());
	}

	public function testEmail(): void {
		$email = 'hans@wurst.de';
		$author = new AuthorTag();

		$this->assertSame($author, $author->setEmail($email));
		$this->assertEquals($email, $author->getEmail());
	}
}
