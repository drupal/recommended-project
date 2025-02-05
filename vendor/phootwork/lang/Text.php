<?php declare(strict_types=1);
/**
 * This file is part of the Phootwork package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @license MIT License
 * @copyright Thomas Gossmann
 */
namespace phootwork\lang;

use phootwork\lang\parts\ArrayConversionsPart;
use phootwork\lang\parts\CheckerPart;
use phootwork\lang\parts\ComparisonPart;
use phootwork\lang\parts\InternalPart;
use phootwork\lang\parts\SearchPart;
use phootwork\lang\parts\TransformationsPart;
use Stringable;

/**
 * Object representation of an immutable String
 *
 * @author gossi
 */
class Text implements Comparable, Stringable {
	use ArrayConversionsPart;
	use CheckerPart;
	use ComparisonPart;
	use SearchPart;
	use InternalPart;
	use TransformationsPart;

	/** @var string */
	private string $string;

	/** @var string */
	private string $encoding;

	/**
	 * Initializes a String object ad assigns both string and encoding properties
	 * the supplied values. $string is cast to a string prior to assignment, and if
	 * $encoding is not specified, it defaults to mb_internal_encoding(). Throws
	 * an InvalidArgumentException if the first argument is an array or object
	 * without a __toString method.
	 *
	 * @param string|Stringable $string   Value to modify, after being cast to string
	 * @param string|null $encoding The character encoding
	 *
	 * @psalm-suppress PossiblyInvalidPropertyAssignmentValue mb_internal_encoding always return string when called as getter
	 */
	public function __construct(string|Stringable $string = '', ?string $encoding = null) {
		$this->string = (string) $string;
		$this->encoding = $encoding ?? mb_internal_encoding();
	}

	/**
	 * Static initializing a String object.
	 *
	 * @param string|Stringable       $string
	 * @param string|null $encoding
	 *
	 * @return static
	 *
	 * @see Text::__construct()
	 *
	 * @psalm-suppress UnsafeInstantiation
	 */
	public static function create(string|Stringable $string, ?string $encoding = null): static {
		return new static($string, $encoding);
	}

	/**
	 * Returns the used encoding
	 *
	 * @return string
	 */
	public function getEncoding(): string {
		return $this->encoding;
	}

	/**
	 * Get string length
	 *
	 * <code>
	 * $str = new Text('Hello World!');<br>
	 * $str->length(); // 12
	 *
	 * $str = new Text('いちりんしゃ');<br>
	 * $str->length(); // 6
	 * </code>
	 *
	 * @return int Returns the length
	 */
	public function length(): int {
		return mb_strlen($this->string, $this->encoding);
	}

	/**
	 * Appends <code>$string</code> and returns as a new <code>Text</code>
	 *
	 * @param string|Stringable $string
	 *
	 * @return Text
	 */
	public function append(string|Stringable $string): self {
		return new self($this->string . $string, $this->encoding);
	}

	/**
	 * Prepends <code>$string</code> and returns as a new <code>Text</code>
	 *
	 * @param string|Stringable $string $string
	 *
	 * @return Text
	 */
	public function prepend(string|Stringable $string): self {
		return new self($string . $this->string, $this->encoding);
	}

	/**
	 * Inserts a substring at the given index
	 *
	 * <code>
	 * $str = new Text('Hello World!');<br>
	 * $str->insert('to this ', 5); // Hello to this World!
	 * </code>
	 *
	 * @param string|Stringable $substring
	 * @param int               $index
	 *
	 * @return Text
	 */
	public function insert(string|Stringable $substring, int $index): self {
		if ($index <= 0) {
			return $this->prepend($substring);
		}

		if ($index > $this->length()) {
			return $this->append($substring);
		}

		$start = mb_substr($this->string, 0, $index, $this->encoding);
		$end = mb_substr($this->string, $index, $this->length(), $this->encoding);

		return new self($start . $substring . $end);
	}

	//
	//
	// SLICING AND SUBSTRING
	//
	//

	/**
	 * Slices a piece of the string from a given offset with a specified length.
	 * If no length is given, the String is sliced to its maximum length.
	 *
	 * @see #substring
	 *
	 * @param int      $offset
	 * @param int|null $length
	 *
	 * @return Text
	 */
	public function slice(int $offset, ?int $length = null): self {
		$offset = $this->prepareOffset($offset);
		$length = $this->prepareLength($offset, $length);

		return new self(mb_substr($this->string, $offset, $length, $this->encoding), $this->encoding);
	}

	/**
	 * Slices a piece of the string from a given start to an end.
	 * If no length is given, the String is sliced to its maximum length.
	 *
	 * @see #slice
	 *
	 * @param int      $start
	 * @param int|null $end
	 *
	 * @return Text
	 */
	public function substring(int $start, ?int $end = null): self {
		$length = $this->length();

		if (null === $end) {
			$end = $length;
		}

		if ($end < 0) {
			$end = $length + $end;
		}

		$end = min($end, $length);
		$start = min($start, $end);
		$end = max($start, $end);
		$end = $end - $start;

		return new self(mb_substr($this->string, $start, $end, $this->encoding), $this->encoding);
	}

	/**
	 * Count the number of substring occurrences.
	 *
	 * @param string|Stringable $substring     The substring to count the occurrencies
	 * @param bool              $caseSensitive Force case-sensitivity
	 *
	 * @return int
	 */
	public function countSubstring(string|Stringable $substring, bool $caseSensitive = true): int {
		if (empty($substring)) {
			throw new \InvalidArgumentException('$substring cannot be empty');
		}

		if ($caseSensitive) {
			return mb_substr_count($this->string, (string) $substring, $this->encoding);
		}
		$str = mb_strtoupper($this->string, $this->encoding);
		$substring = mb_strtoupper((string) $substring, $this->encoding);

		return mb_substr_count($str, $substring, $this->encoding);
	}

	//
	//
	// REPLACING
	//
	//

	/**
	 * Replace all occurrences of the search string with the replacement string
	 *
	 * @see #supplant
	 *
	 * @param Arrayable|Stringable|array|string $search
	 * 		The value being searched for, otherwise known as the needle. An array may be used
	 * 		to designate multiple needles.
	 * @param Arrayable|Stringable[]|array|string $replace
	 * 		The replacement value that replaces found search values. An array may be used to
	 * 		designate multiple replacements.
	 *
	 * @return Text
	 *
	 * @psalm-suppress MixedArgumentTypeCoercion
	 */
	public function replace(Arrayable|Stringable|array|string $search, Arrayable|Stringable|array|string $replace): self {
		$search = $search instanceof Stringable ? (string) $search :
			($search instanceof Arrayable ? $search->toArray() : $search);
		$replace = $replace instanceof Stringable ? (string) $replace :
			($replace instanceof Arrayable ? $replace->toArray() : $replace);

		return new self(str_replace($search, $replace, $this->string), $this->encoding);
	}

	/**
	 * Replaces all occurrences of given replacement map. Keys will be replaced with its values.
	 *
	 * @param string[] $map the replacements. Keys will be replaced with its value.
	 *
	 * @return Text
	 */
	public function supplant(array $map): self {
		return new self(str_replace(array_keys($map), array_values($map), $this->string), $this->encoding);
	}

	/**
	 * Replace text within a portion of a string.
	 *
	 * @param string|Stringable $replacement
	 * @param int               $offset
	 * @param int|null          $length
	 *
	 * @return Text
	 */
	public function splice(string|Stringable $replacement, int $offset, ?int $length = null): self {
		$offset = $this->prepareOffset($offset);
		$length = $this->prepareLength($offset, $length);

		$start = $this->substring(0, $offset);
		$end = $this->substring($offset + $length);

		return new self($start . $replacement . $end);
	}

	//
	//
	// STRING OPERATIONS
	//
	//

	/**
	 * Strip whitespace (or other characters) from the beginning and end of the string
	 *
	 * @param string|Stringable $characters
	 *        Optionally, the stripped characters can also be specified using the mask parameter.
	 *        Simply list all characters that you want to be stripped. With .. you can specify a
	 *        range of characters.
	 *
	 * @return Text
	 */
	public function trim(string|Stringable $characters = " \t\n\r\v\0"): self {
		return new self(trim($this->string, (string) $characters), $this->encoding);
	}

	/**
	 * Strip whitespace (or other characters) from the beginning of the string
	 *
	 * @param string|Stringable $characters
	 *        Optionally, the stripped characters can also be specified using the mask parameter.
	 *        Simply list all characters that you want to be stripped. With .. you can specify a
	 *        range of characters.
	 *
	 * @return Text
	 */
	public function trimStart(string|Stringable $characters = " \t\n\r\v\0"): self {
		return new self(ltrim($this->string, (string) $characters), $this->encoding);
	}

	/**
	 * Strip whitespace (or other characters) from the end of the string
	 *
	 * @param string|Stringable $characters
	 *        Optionally, the stripped characters can also be specified using the mask parameter.
	 *        Simply list all characters that you want to be stripped. With .. you can specify a
	 *        range of characters.
	 *
	 * @return Text
	 */
	public function trimEnd(string|Stringable $characters = " \t\n\r\v\0"): self {
		return new self(rtrim($this->string, (string) $characters), $this->encoding);
	}

	/**
	 * Adds padding to the start and end
	 *
	 * @param int               $length
	 * @param string|Stringable $padding
	 *
	 * @return Text
	 */
	public function pad(int $length, string|Stringable $padding = ' '): self {
		$len = $length - $this->length();

		return $this->applyPadding(floor($len / 2), ceil($len / 2), $padding);
	}

	/**
	 * Adds padding to the start
	 *
	 * @param int               $length
	 * @param string|Stringable $padding
	 *
	 * @return Text
	 */
	public function padStart(int $length, string|Stringable $padding = ' ') {
		return $this->applyPadding($length - $this->length(), 0, $padding);
	}

	/**
	 * Adds padding to the end
	 *
	 * @param int               $length
	 * @param string|Stringable $padding
	 *
	 * @return Text
	 */
	public function padEnd(int $length, string|Stringable $padding = ' '): self {
		return $this->applyPadding(0, $length - $this->length(), $padding);
	}

	/**
	 * Adds the specified amount of left and right padding to the given string.
	 * The default character used is a space.
	 *
	 * @see https://github.com/danielstjules/Stringy/blob/master/src/Stringy.php
	 *
	 * @param int|float $left Length of left padding
	 * @param int|float $right Length of right padding
	 * @param string|Stringable $padStr String used to pad
	 *
	 * @return Text the padded string
	 */
	protected function applyPadding(int|float $left = 0, int|float $right = 0, string|Stringable $padStr = ' '): self {
		$length = mb_strlen((string) $padStr, $this->encoding);
		$strLength = $this->length();
		$paddedLength = $strLength + $left + $right;
		if (!$length || $paddedLength <= $strLength) {
			return $this;
		}

		$leftPadding = mb_substr(str_repeat((string) $padStr, (int) ceil($left / $length)), 0, (int) $left, $this->encoding);
		$rightPadding = mb_substr(str_repeat((string) $padStr, (int) ceil($right / $length)), 0, (int) $right, $this->encoding);

		return new self($leftPadding . $this->string . $rightPadding);
	}

	/**
	 * Ensures a given substring at the start of the string
	 *
	 * @param string $substring
	 *
	 * @return Text
	 */
	public function ensureStart(string $substring): self {
		if (!$this->startsWith($substring)) {
			return $this->prepend($substring);
		}

		return $this;
	}

	/**
	 * Ensures a given substring at the end of the string
	 *
	 * @param string $substring
	 *
	 * @return Text
	 */
	public function ensureEnd(string $substring): self {
		if (!$this->endsWith($substring)) {
			return $this->append($substring);
		}

		return $this;
	}

	/**
	 * Returns a copy of the string wrapped at a given number of characters
	 *
	 * @param int $width The number of characters at which the string will be wrapped.
	 * @param string $break The line is broken using the optional break parameter.
	 * @param bool $cut
	 * 		If the cut is set to TRUE, the string is always wrapped at or before the specified
	 * 		width. So if you have a word that is larger than the given width, it is broken apart.
	 *
	 * @return Text Returns the string wrapped at the specified length.
	 */
	public function wrapWords(int $width = 75, string $break = "\n", bool $cut = false): self {
		return new self(wordwrap($this->string, $width, $break, $cut), $this->encoding);
	}

	/**
	 * Repeat the string $times times. If $times is 0, it returns ''.
	 *
	 * @param int $multiplier
	 *
	 * @throws \InvalidArgumentException If $times is negative.
	 *
	 * @return Text
	 */
	public function repeat(int $multiplier): self {
		return new self(str_repeat($this->string, $multiplier), $this->encoding);
	}

	/**
	 * Reverses the character order
	 *
	 * @return Text
	 */
	public function reverse(): self {
		return new self(strrev($this->string), $this->encoding);
	}

	/**
	 * Truncates the string with a substring and ensures it doesn't exceed the given length
	 *
	 * @param int $length
	 * @param string $substring
	 *
	 * @return Text
	 */
	public function truncate(int $length, string $substring = ''): self {
		if ($this->length() <= $length) {
			return new self($this->string, $this->encoding);
		}

		$substrLen = mb_strlen($substring, $this->encoding);

		if ($this->length() + $substrLen > $length) {
			$length -= $substrLen;
		}

		return $this->substring(0, $length)->append($substring);
	}

	/**
	 * Returns the native string
	 *
	 * @return string
	 */
	public function toString(): string {
		return $this->string;
	}

	protected function getString(): string {
		return $this->toString();
	}

	//
	//
	// MAGIC HAPPENS HERE
	//
	//

	public function __toString(): string {
		return $this->string;
	}
}
