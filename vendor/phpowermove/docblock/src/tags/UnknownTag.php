<?php declare(strict_types=1);
/*
 * This file is part of the Docblock package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @license MIT License
 */

namespace phpowermove\docblock\tags;

/**
 * Represents an unknown tag.
 */
class UnknownTag extends AbstractDescriptionTag {
	protected function parse(string $content): void {
		$this->setDescription($content);
	}

	public function toString(): string {
		return sprintf('@%s %s', $this->tagName, $this->description);
	}
}
