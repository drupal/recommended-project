<?php declare(strict_types=1);
/**
 * This file is part of the Phootwork package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @license MIT License
 * @copyright Thomas Gossmann
 */
namespace phootwork\lang\parts;

use phootwork\lang\ArrayObject;
use phootwork\lang\Text;
use Stringable;

/**
 * Text searching methods
 *
 * @author ThomasGossmann
 * @author Cristiano Cinotti
 */
trait SearchPart {
	abstract protected function getString(): string;

	abstract public function length(): int;

	/**
	 * Returns the character at the given zero-related index
	 *
	 * <code>
	 * $str = new Text('Hello World!');<br>
	 * $str->at(6); // W
	 *
	 * $str = new Text('いちりんしゃ');<br>
	 * $str->at(4) // し
	 * </code>
	 *
	 * @param int $index zero-related index
	 *
	 * @return string the found character
	 */
	public function at(int $index): string {
		return mb_substr($this->getString(), $index, 1, $this->encoding);
	}

	/**
	 * Returns an ArrayObject consisting of the characters in the string.
	 *
	 * @return ArrayObject An ArrayObject of all chars
	 */
	public function chars(): ArrayObject {
		return new ArrayObject(mb_str_split($this->getString(), 1, $this->encoding));
	}

	/**
	 * Returns the index of a given string, starting at the optional zero-related offset
	 *
	 * @param string|Stringable $string
	 * @param int               $offset zero-related offset
	 *
	 * @return int|null int for the index or null if the given string doesn't occur
	 */
	public function indexOf(string|Stringable $string, int $offset = 0): ?int {
		$output = mb_strpos($this->getString(), (string) $string, $offset, $this->encoding);

		return false === $output ? null : $output;
	}

	/**
	 * Returns the last index of a given string, starting at the optional offset
	 *
	 * @param string|Stringable $string $string
	 * @param int|null          $offset
	 *
	 * @return int|null int for the index or null if the given string doesn't occur
	 */
	public function lastIndexOf(string|Stringable $string, ?int $offset = null): ?int {
		if (null === $offset) {
			$offset = $this->length();
		}

		// Converts $offset to a negative offset as strrpos has a different
		// behavior for positive offsets.
		$output = mb_strrpos($this->getString(), (string) $string, $offset - $this->length(), $this->encoding);

		return false === $output ? null : $output;
	}

	/**
	 * Checks whether the string starts with the given string. Case sensitive!
	 *
	 * @param string|Stringable $substring The substring to look for
	 *
	 * @return bool
	 *
	 * @see Text::startsWithIgnoreCase()
	 *
	 */
	public function startsWith(string|Stringable $substring): bool {
		return str_starts_with($this->getString(), (string) $substring);
	}

	/**
	 * Checks whether the string starts with the given string. Ignores case.
	 *
	 * @param string|Stringable $substring The substring to look for
	 *
	 * @return bool
	 *
	 * @see Text::startsWith()
	 *
	 */
	public function startsWithIgnoreCase(string|Stringable $substring): bool {
		return str_starts_with($this->toUpperCase()->getString(), mb_strtoupper((string) $substring, $this->encoding));
	}

	/**
	 * Checks whether the string ends with the given string. Case sensitive!
	 *
	 * @param string|Stringable $substring The substring to look for
	 *
	 * @return bool
	 *
	 * @see Text::endsWithIgnoreCase()
	 *
	 */
	public function endsWith(string|Stringable $substring): bool {
		return str_ends_with($this->getString(), (string) $substring);
	}

	/**
	 * Checks whether the string ends with the given string. Ingores case.
	 *
	 * @param string|Stringable $substring The substring to look for
	 *
	 * @return bool
	 *
	 * @see Text::endsWith()
	 *
	 */
	public function endsWithIgnoreCase(string|Stringable $substring): bool {
		return str_ends_with($this->toUpperCase()->getString(), mb_strtoupper((string) $substring, $this->encoding));
	}

	/**
	 * Checks whether the given string occurs
	 *
	 * @param string|Stringable $text
	 *
	 * @return bool
	 */
	public function contains(Stringable|string $text): bool {
		return str_contains($this->getString(), (string) $text);
	}

	/**
	 * Performs a regular expression matching with the given regexp
	 *
	 * @param string $regexp
	 *
	 * @return bool
	 */
	public function match(string $regexp): bool {
		return (bool) preg_match($regexp, $this->getString());
	}
}
