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

use InvalidArgumentException;
use phootwork\lang\ArrayObject;
use phootwork\lang\Text;

/**
 * Text methods for array/ArrayObject conversions
 *
 * @author Thomas Gossmann
 * @author Cristiano Cinotti
 */
trait ArrayConversionsPart {
	abstract protected function getString(): string;

	/**
	 * Splits the string by string
	 *
	 * @param string $delimiter The boundary string.
	 * @param int $limit
	 * 		If limit is set and positive, the returned array will contain a maximum of
	 * 		limit elements with the last element containing the rest of string.
	 *
	 * 		If the limit parameter is negative, all components except the last
	 * 		-limit are returned.
	 *
	 * 		If the limit parameter is zero, then this is treated as 1.
	 *
	 * @throws InvalidArgumentException If the delimiter is an empty string.
	 *
	 * @return ArrayObject
	 * 		Returns an array of strings created by splitting the string parameter on boundaries
	 * 		formed by the delimiter.
	 *
	 *        If delimiter contains a value that is not contained in string and a negative limit is used,
	 *        then an empty array will be returned, otherwise an array containing string will be returned.
	 *
	 */
	public function split(string $delimiter, int $limit = PHP_INT_MAX): ArrayObject {
		if ('' === $delimiter) {
			throw new InvalidArgumentException("The delimiter can't be an empty string");
		}

		return new ArrayObject(explode($delimiter, $this->getString(), $limit));
	}

	/**
	 * Join array elements with a string
	 *
	 * @param array       $pieces   The array of strings to join.
	 * @param string      $glue     Defaults to an empty string.
	 * @param string|null $encoding the desired encoding
	 *
	 * @return Text
	 *        Returns a string containing a string representation of all the array elements in the
	 *        same order, with the glue string between each element.
	 *
	 * @psalm-suppress MixedArgumentTypeCoercion
	 */
	public static function join(array $pieces, string $glue = '', ?string $encoding = null): Text {
		array_map(
			function (mixed $element): void {
				if (!($element === null || is_scalar($element) || $element instanceof \Stringable)) {
					throw new \TypeError('Can join elements only if scalar, null or \\Stringable');
				}
			},
			$pieces
		);

		return new Text(implode($glue, $pieces), $encoding);
	}

	/**
	 * Convert the string to an array
	 *
	 * @param int $splitLength Maximum length of the chunk.
	 *
	 * @throws InvalidArgumentException If splitLength is less than 1.
	 *
	 * @return ArrayObject
	 * 		If the optional splitLength parameter is specified, the returned array will be
	 * 		broken down into chunks with each being splitLength in length, otherwise each chunk
	 * 		will be one character in length.
	 *      If the split_length length exceeds the length of string, the entire string is returned
	 *      as the first (and only) array element.
	 */
	public function chunk(int $splitLength = 1): ArrayObject {
		if (false === $array = str_split($this->getString(), $splitLength)) {
			throw new InvalidArgumentException('The chunk length has to be positive');
		}

		return new ArrayObject($array);
	}
}
