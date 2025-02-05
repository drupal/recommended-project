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

/**
 * Tag Factory
 */
class TagFactory {
	/**
	 * Creates a new tag instance on the given name
	 * 
	 * @param string $tagName
	 * @param string $content
	 *
	 * @return AbstractTag
	 *
	 * @psalm-suppress MoreSpecificReturnType
	 * @psalm-suppress LessSpecificReturnStatement
	 */
	public static function create(string $tagName, string $content = ''): AbstractTag {
		$class = Text::create($tagName)
			->toStudlyCase()
			->prepend('phpowermove\\docblock\\tags\\')
			->append('Tag')
			->toString()
		;

		if (!class_exists($class)) {
			return (new UnknownTag($content))->setTagName($tagName);
		}

		/** @psalm-suppress MixedMethodCall */
		return new $class($content);
	}
}
