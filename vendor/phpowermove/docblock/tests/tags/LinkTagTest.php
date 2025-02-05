<?php declare(strict_types=1);

namespace phpowermove\docblock\tests\tags;

use phpowermove\docblock\tags\LinkTag;
use PHPUnit\Framework\TestCase;

class LinkTagTest extends TestCase {
	public function testReadWrite(): void {
		$link = new LinkTag('http://example.com');
		$this->assertEquals('http://example.com', $link->getUrl());
		$this->assertEquals('@link http://example.com', $link->toString());

		$link = new LinkTag('http://example.com desc');
		$this->assertEquals('http://example.com', $link->getUrl());
		$this->assertEquals('desc', $link->getDescription());
		$this->assertEquals('@link http://example.com desc', $link->toString());

		$link = new LinkTag('http://.example.com desc');
		$this->assertEmpty($link->getUrl());
		$this->assertEquals('http://.example.com desc', $link->getDescription());
	}

	public function testUrl(): void {
		$url = 'http://example.com';
		$link = new LinkTag();

		$this->assertSame($link, $link->setUrl($url));
		$this->assertEquals($url, $link->getUrl());
	}

	public function testDescription(): void {
		$desc = 'desc';
		$link = new LinkTag();

		$this->assertSame($link, $link->setDescription($desc));
		$this->assertEquals($desc, $link->getDescription());
	}
}
