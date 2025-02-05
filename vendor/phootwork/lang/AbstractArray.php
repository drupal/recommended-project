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

/**
 * Superclass containing property and methods shared between
 * `phootwork\lang\ArrayObject` and `phootwork\class\AbstractCollection`
 *
 * @author Cristiano Cinotti
 */
abstract class AbstractArray implements \Countable {
	/** @var array */
	protected array $array = [];

	abstract public function __construct(array $contents = []);

	/**
	 * Counts the array
	 *
	 * @return int the amount of items
	 */
	public function count(): int {
		return count($this->array);
	}

	/**
	 * Return the size of the array.
	 * Alias of `count`
	 *
	 * @return int
	 */
	public function size(): int {
		return $this->count();
	}

	/**
	 * @psalm-suppress UnsafeInstantiation
	 *
	 * @return $this
	 */
	public function __clone() {
		return new static($this->array);
	}

	/**
	 * Checks whether the given element is in this array
	 *
	 * @param mixed $element
	 *
	 * @return bool
	 */
	public function contains(mixed $element): bool {
		return in_array($element, $this->array, true);
	}

	/**
	 * Checks whether this array is empty
	 *
	 * @return bool
	 */
	public function isEmpty(): bool {
		return count($this->array) === 0;
	}

	/**
	 * Searches the array with a given callback and returns the first element if found.
	 *
	 * The callback function takes one or two parameters:
	 *
	 *     function ($element [, $query]) {}
	 *
	 * The callback must return a boolean
	 * When it's passed, $query must be the first argument:
	 *
	 *     - find($query, callback)
	 *     - find(callback)
	 *
	 * @param array $arguments
	 *
	 * @return mixed the found element or null if it hasn't been found
	 *
	 * @psalm-suppress MixedAssignment
	 * @psalm-suppress MixedFunctionCall
	 */
	public function find(mixed ...$arguments): mixed {
		foreach ($this->array as $element) {
			$return = count($arguments) === 1 ? $arguments[0]($element) : $arguments[1]($element, $arguments[0]);

			if ($return) {
				return $element;
			}
		}

		return null;
	}

	/**
	 * Searches the array with a given callback and returns the last element if found.
	 *
	 * The callback function takes one or two parameters:
	 *
	 *     function ($element [, $query]) {}
	 *
	 * The callback must return a boolean
	 * When it's passed, $query must be the first argument:
	 *
	 *     - find($query, callback)
	 *     - find(callback)
	 *
	 * @param array $arguments
	 *
	 * @return mixed the found element or null if it hasn't been found
	 *
	 * @psalm-suppress MixedAssignment
	 * @psalm-suppress MixedFunctionCall
	 */
	public function findLast(mixed ...$arguments): mixed {
		$reverse = array_reverse($this->array, true);
		foreach ($reverse as $element) {
			$return = count($arguments) === 1 ? $arguments[0]($element) : $arguments[1]($element, $arguments[0]);

			if ($return) {
				return $element;
			}
		}

		return null;
	}

	/**
	 * Searches the array with a given callback and returns all matching elements.
	 *
	 * The callback function takes one or two parameters:
	 *
	 *     function ($element [, $query]) {}
	 *
	 * The callback must return a boolean
	 * When it's passed, $query must be the first argument:
	 *
	 *     - find($query, callback)
	 *     - find(callback)
	 *
	 * @param mixed ...$arguments
	 *
	 * @return static an ArrayObject or a collection of found elements
	 *
	 * @psalm-suppress UnsafeInstantiation
	 * @psalm-suppress MixedAssignment
	 * @psalm-suppress MixedFunctionCall
	 */
	public function findAll(mixed ...$arguments): static {
		$array = [];

		foreach ($this->array as $k => $element) {
			$return = count($arguments) === 1 ? $arguments[0]($element) : $arguments[1]($element, $arguments[0]);

			if ($return) {
				$array[$k] = $element;
			}
		}

		return new static($array);
	}

	/**
	 * Searches the array for query using the callback function on each element
	 *
	 * The callback function takes one or two parameters:
	 *
	 *     function ($element [, $query]) {}
	 *
	 * The callback must return a boolean
	 * When it's passed, $query must be the first argument:
	 *
	 *     - search($query, callback)
	 *     - search(callback)
	 *
	 * @param mixed ...$arguments
	 *
	 * @return bool
	 *
	 * @psalm-suppress MixedFunctionCall
	 * @psalm-suppress MixedAssignment
	 */
	public function search(mixed ...$arguments): bool {
		foreach ($this->array as $element) {
			$return = count($arguments) === 1 ? $arguments[0]($element) : $arguments[1]($element, $arguments[0]);

			if ($return) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Returns the php array type
	 *
	 * @return array
	 */
	public function toArray(): array {
		return $this->array;
	}

	/**
	 * Sorts the array
	 *
	 * @param Comparator|callable|null $cmp
	 *
	 * @return $this
	 */
	public function sort(Comparator|callable|null $cmp = null): self {
		$this->doSort($this->array, 'usort', 'sort', $cmp);

		return $this;
	}

	/**
	 * Internal sort function
	 *
	 * @param array                    $array the array on which is operated on
	 * @param callable                 $usort the sort function for user passed $cmd
	 * @param callable                 $sort  the default sort function
	 * @param Comparator|callable|null $cmp   the compare function
	 */
	protected function doSort(array & $array, callable $usort, callable $sort, Comparator|callable|null $cmp = null): void {
		if (is_callable($cmp)) {
			$usort($array, $cmp);
		} elseif ($cmp instanceof Comparator) {
			$usort(
				$array,
				function (mixed $a, mixed $b) use ($cmp): int {
					return $cmp->compare($a, $b);
				}
			);
		} else {
			$sort($array);
		}
	}

	/**
	 * Tests whether all elements in the array pass the test implemented by the provided function.
	 *
	 * Returns <code>true</code> for an empty array.
	 *
	 * @param callable(mixed, mixed): scalar $callback
	 *
	 * @return bool
	 */
	public function every(callable $callback): bool {
		return $this->count() === count(array_filter($this->array, $callback));
	}

	/**
	 * Tests whether at least one element in the array passes the test implemented by the provided function.
	 *
	 * Returns <code>false</code> for an empty array.
	 *
	 * @param callable(mixed, mixed): scalar $callback
	 *
	 * @return bool
	 */
	public function some(callable $callback): bool {
		return count(array_filter($this->array, $callback)) > 0;
	}

	/**
	 * Filters elements using a callback function
	 *
	 * @param callable(mixed, mixed): scalar $callback the filter function
	 *
	 * @return static
	 *
	 * @psalm-suppress UnsafeInstantiation
	 */
	public function filter(callable $callback): self {
		return new static(array_filter($this->array, $callback));
	}

	/**
	 * Applies the callback to the elements
	 *
	 * @param callable $callback the applied callback function
	 *
	 * @return static
	 *
	 * @psalm-suppress UnsafeInstantiation
	 */
	public function map(callable $callback): self {
		return new static(array_map($callback, $this->array));
	}
}
