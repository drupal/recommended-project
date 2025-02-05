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

use phootwork\lang\Text;

/**
 * Text methods to perform string and Text object comparison
 *
 * @author Thomas Gossmann
 * @author Cristiano Cinotti
 */
trait ComparisonPart {
	abstract protected function getString(): string;

	/**
	 * Compares this string to another
	 *
	 * @param mixed $comparison
	 *
	 * @return int
	 *
	 * @see \phootwork\lang\Comparable::compareTo()
	 */
	public function compareTo(mixed $comparison): int {
		if (is_string($comparison) || $comparison instanceof Text) {
			return $this->compare($comparison);
		}

		throw new \InvalidArgumentException('`compareTo` method can accept only strings or Text objects.');
	}

	/**
	 * Compares this string to another string, ignoring the case
	 *
	 * @param string|Text $compare
	 *
	 * @return int Return Values:<br>
	 * 		&lt; 0 if the object is less than comparison<br>
	 *  	&gt; 0 if the object is greater than comparison<br>
	 * 		0 if they are equal.
	 */
	public function compareCaseInsensitive(string|Text $compare): int {
		return $this->compare($compare, 'strcasecmp');
	}

	/**
	 * Compares this string to another
	 *
	 * @param string|Text   $compare string to compare to
	 * @param callable|null $callback
	 *
	 * @return int
	 *
	 * @psalm-suppress MixedInferredReturnType
	 */
	public function compare(string|Text $compare, ?callable $callback = null): int {
		if ($callback === null) {
			$callback = 'strcmp';
		}

		return $callback($this->getString(), (string) $compare);
	}

	/**
	 * Checks whether the string and the given object are equal
	 *
	 * @param string|Text $string
	 *
	 * @return bool
	 */
	public function equals(string|Text $string): bool {
		return $this->compareTo($string) === 0;
	}

	/**
	 * Checks whether the string and the given object are equal ignoring the case
	 *
	 * @param string|Text $string
	 *
	 * @return bool
	 */
	public function equalsIgnoreCase(string|Text $string): bool {
		return $this->compareCaseInsensitive($string) === 0;
	}
}
