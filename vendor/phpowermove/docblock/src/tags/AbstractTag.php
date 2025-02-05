<?php declare(strict_types=1);
/*
 * This file is part of the Docblock package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @license MIT License
 */

namespace phpowermove\docblock\tags;

use phootwork\lang\Text;

abstract class AbstractTag implements \Stringable {
	protected string $tagName = '';

	/**
	 * Creates a new tag instance
	 *
	 * @param string $content
	 *
	 * @return $this
	 */
	public static function create(string $content = ''): self {
		return new static($content);
	}

	/**
	 * Creates a new tag instance
	 *
	 * @param string $content
	 */
	final public function __construct(string $content = '') {
		$this->tagName = Text::create(get_class($this))
			->trimStart('phpowermove\\docblock\\tags\\')
			->trimEnd('Tag')
			->toKebabCase()
			->toString()
		;
		$this->parse($content);
	}

	/**
	 * Returns the tag name.
	 *
	 * @return string the tag name
	 */
	public function getTagName(): string {
		return $this->tagName;
	}

	public function setTagName(string $tagName): self {
		$this->tagName = $tagName;

		return $this;
	}

	/**
	 * Parses the given string
	 *
	 * @param string $content
	 */
	abstract protected function parse(string $content): void;

	abstract public function toString(): string;

	/**
	 * Magic toString() method
	 *
	 * @return string
	 */
	public function __toString(): string {
		return $this->toString();
	}
}
