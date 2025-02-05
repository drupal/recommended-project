<?php declare(strict_types=1);
/*
 * This file is part of the Docblock package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @license MIT License
 */

namespace phpowermove\docblock\tests;

use phootwork\collection\ArrayList;
use phpowermove\docblock\TagNameComparator;
use PHPUnit\Framework\TestCase;

class TagNameComparatorTest extends TestCase {
	public function testComparison(): void {
		$list = new ArrayList(['author', 'since', 'see', 'param', 'author']);
		$list->sort(new TagNameComparator());

		$this->assertEquals(['see', 'author', 'author', 'since', 'param'], $list->toArray());
	}

	public function testComparisonWithInvalidTag(): void {
		$list = new ArrayList(['invalid_tag', 'author', 'since', 'see', 'param', 'author']);
		$list->sort(new TagNameComparator());

		$this->assertEquals(['invalid_tag', 'see', 'author', 'author', 'since', 'param'], $list->toArray());
	}
}
