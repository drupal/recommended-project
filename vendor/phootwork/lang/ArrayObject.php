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

use phootwork\lang\parts\AccessorsPart;
use phootwork\lang\parts\AddPart;
use phootwork\lang\parts\EachPart;
use phootwork\lang\parts\IndexFindersPart;
use phootwork\lang\parts\InsertPart;
use phootwork\lang\parts\PopPart;
use phootwork\lang\parts\ReducePart;
use phootwork\lang\parts\RemovePart;
use phootwork\lang\parts\ReversePart;
use phootwork\lang\parts\SortAssocPart;

class ArrayObject extends AbstractArray implements \ArrayAccess, \Countable, \IteratorAggregate, \Serializable, Arrayable {
	use AccessorsPart;
	use AddPart;
	use EachPart;
	use IndexFindersPart;
	use InsertPart;
	use PopPart;
	use ReducePart;
	use RemovePart;
	use ReversePart;
	use SortAssocPart;

	public function __construct(array $contents = []) {
		$this->array = $contents;
	}

	public function __serialize(): array {
		return $this->array;
	}

	public function __unserialize(array $data): void {
		$this->array = $data;
	}

	public function getIterator(): \ArrayIterator {
		return new \ArrayIterator($this->array);
	}

	public function serialize(): string {
		return serialize($this->array);
	}

	/**
	 * @psalm-suppress MethodSignatureMismatch `\Serializable::unserialize` return `void` but we want fluid interface.
	 * @psalm-suppress MixedAssignment if `unserialize($serialized)` can't return an array, this assignment throws
	 *                 a TypeError exception, which is ok for us
	 */
	public function unserialize(string $serialized): self {
		$this->array = unserialize($serialized);

		return $this;
	}

	/**
	 * Resets the array
	 *
	 * @return $this
	 */
	public function clear(): self {
		$this->array = [];

		return $this;
	}

	//
	//
	// MUTATIONS
	//
	//

	/**
	 * Append one or more elements onto the end of array
	 *
	 * @param array $elements
	 *
	 * @return $this
	 */
	public function append(mixed ...$elements): self {
		array_push($this->array, ...$elements);

		return $this;
	}

	/**
	 * Prepend one or more elements to the beginning of the array
	 *
	 * @param array $elements
	 *
	 * @return $this
	 */
	public function prepend(mixed ...$elements): self {
		array_unshift($this->array, ...$elements);

		return $this;
	}

	/**
	 * Shift an element off the beginning of array
	 *
	 * @return mixed the shifted element
	 */
	public function shift(): mixed {
		return array_shift($this->array);
	}

	/**
	 * Remove a portion of the array and replace it with something else
	 *
	 * @param int      $offset      If offset is positive then the start of removed portion is at that offset from the beginning of the input array. If offset is negative then it starts that far from the end of the input array.
	 * @param int|null $length      If length is omitted, removes everything from offset to the end of the array. If length is specified and is positive, then that many elements will be removed. If length is specified and is negative then the end of the removed portion will be that many elements from the end of the array. If length is specified and is zero, no elements will be removed.
	 * @param array    $replacement If replacement array is specified, then the removed elements are replaced with elements from this array. If offset and length are such that nothing is removed, then the elements from the replacement array are inserted in the place specified by the offset. Note that keys in replacement array are not preserved. If replacement is just one element it is not necessary to put array() around it, unless the element is an array itself, an object or NULL.
	 *
	 * @return $this
	 *
	 * @psalm-suppress PossiblyNullArgument third argument of `array_splice` CAN be null
	 */
	public function splice(int $offset, ?int $length = null, array $replacement = []): self {
		array_splice($this->array, $offset, $length, $replacement);

		return $this;
	}

	//
	//
	// SUGAR
	//
	//

	/**
	 * Joins the array with a string
	 *
	 * @param string $glue Defaults to an empty string.
	 *
	 * @return Text
	 * 		Returns a string containing a string representation of all the array elements in the
	 * 		same order, with the glue string between each element.
	 *
	 * @psalm-suppress MixedArgumentTypeCoercion
	 */
	public function join(string $glue = ''): Text {
		array_map(
			function (mixed $element): void {
				if (!($element === null || is_scalar($element) || $element instanceof \Stringable)) {
					throw new \TypeError('Can join elements only if scalar, null or \\Stringable');
				}
			},
			$this->array
		);

		return new Text(implode($glue, $this->array));
	}

	/**
	 * Extract a slice of the array
	 *
	 * @param int      $offset
	 * @param int|null $length
	 * @param bool     $preserveKeys
	 *
	 * @return ArrayObject
	 */
	public function slice(int $offset, ?int $length = null, bool $preserveKeys = false): self {
		return new self(array_slice($this->array, $offset, $length, $preserveKeys));
	}

	/**
	 * Merges in other values
	 *
	 * @param array ...$toMerge Variable list of arrays to merge.
	 *
	 * @return ArrayObject $this
	 */
	public function merge(mixed ...$toMerge): self {
		$this->array = array_merge($this->array, ...$toMerge);

		return $this;
	}

	/**
	 * Merges in other values, recursively
	 *
	 * @param array ...$toMerge Variable list of arrays to merge.
	 *
	 * @return ArrayObject $this
	 */
	public function mergeRecursive(mixed ...$toMerge): self {
		$this->array = array_merge_recursive($this->array, ...$toMerge);

		return $this;
	}

	/**
	 * Returns the keys of the array
	 *
	 * @return ArrayObject the keys
	 */
	public function keys(): self {
		return new self(array_keys($this->array));
	}

	/**
	 * Returns the values of the array
	 *
	 * @return ArrayObject the values
	 */
	public function values(): self {
		return new self(array_values($this->array));
	}

	/**
	 * Flips keys and values
	 *
	 * @return ArrayObject $this
	 */
	public function flip(): self {
		$this->array = array_flip($this->array);

		return $this;
	}

	//
	//
	// INTERNALS
	//
	//

	/**
	 * @param int|string|null $offset
	 * @param mixed $value
	 *
	 * @internal
	 */
	public function offsetSet(mixed $offset, mixed $value): void {
		if (!is_null($offset)) {
			$this->array[$offset] = $value;
		}
	}

	/**
	 * @param int|string $offset
	 *
	 * @return bool
	 *
	 * @internal
	 */
	public function offsetExists(mixed $offset): bool {
		return isset($this->array[$offset]);
	}

	/**
	 * @param int|string $offset
	 *
	 * @internal
	 */
	public function offsetUnset(mixed $offset): void {
		unset($this->array[$offset]);
	}

	/**
	 * @param int|string $offset
	 *
	 * @return mixed
	 *
	 * @internal
	 */
	public function offsetGet(mixed $offset): mixed {
		return $this->array[$offset] ?? null;
	}
}
