<?php declare(strict_types=1);
/**
 * This file is part of the Phootwork package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @license MIT License
 * @copyright Thomas Gossmann
 */
namespace phootwork\collection;

use Iterator;
use phootwork\lang\AbstractArray;
use phootwork\lang\Comparator;
use phootwork\lang\parts\SortAssocPart;
use phootwork\lang\Text;

/**
 * Represents a Map
 * 
 * @author Thomas Gossmann
 */
class Map extends AbstractCollection implements \ArrayAccess {
	use SortAssocPart;

	/**
	 * Creates a new Map
	 * 
	 * @param array|Iterator $collection
	 */
	public function __construct(array|Iterator $collection = []) {
		$this->setAll($collection);
	}

	/**
	 * Sets an element with the given key on that map
	 * 
	 * @param string|int|Text $key
	 * @param mixed $element
	 *
	 * @return Map $this
	 */
	public function set(string|int|Text $key, mixed $element): self {
		$key = is_int($key) ? $key : (string) $key;
		$this->array[$key] = $element;

		return $this;
	}

	/**
	 * Returns the element for the given key
	 * 
	 * @param string|int|Text $key
	 *
	 * @return mixed
	 */
	public function get(string|int|Text $key): mixed {
		$key = is_int($key) ? $key : (string) $key;

		return $this->array[$key] ?? null;
	}

	/**
	 * Returns the key for the given value or null if not found
	 *
	 * @param mixed $value the value
	 *
	 * @return string|null
	 */
	public function getKey(mixed $value): ?string {
		/**
		 * @var string $k
		 * @var mixed $v
		 */
		foreach ($this->array as $k => $v) {
			if ($v === $value) {
				return $k;
			}
		}

		return null;
	}

	/**
	 * Sets many elements on that map
	 *
	 * @param array|Iterator $collection
	 *
	 * @return Map $this
	 */
	public function setAll(array|Iterator $collection): self {
		/**
		 * @var string $key
		 * @var mixed $element
		 */
		foreach ($collection as $key => $element) {
			$this->set($key, $element);
		}

		return $this;
	}

	/**
	 * Removes and returns an element from the map by the given key. Returns null if the key
	 * does not exist.
	 * 
	 * @param string|Text $key
	 *
	 * @return $this
	 */
	public function remove(string|Text $key): self {
		if (isset($this->array[(string) $key])) {
			unset($this->array[(string) $key]);
		}

		return $this;
	}

	/**
	 * Returns all keys as Set
	 * 
	 * @return Set the map's keys
	 */
	public function keys(): Set {
		return new Set(array_keys($this->array));
	}

	/**
	 * Returns all values as ArrayList
	 * 
	 * @return ArrayList the map's values
	 */
	public function values(): ArrayList {
		return new ArrayList(array_values($this->array));
	}

	/**
	 * Returns whether the key exist.
	 * 
	 * @param string|Text $key
	 *
	 * @return bool
	 */
	public function has(string|Text $key): bool {
		return isset($this->array[(string) $key]);
	}

	/**
	 * Sorts the map
	 *
	 * @param Comparator|callable|null $cmp
	 *
	 * @return $this
	 */
	public function sort(Comparator|callable|null $cmp = null): AbstractArray {
		return $this->sortAssoc($cmp);
	}

	/**
	 * Iterates the map and calls the callback function with the current key and value as parameters
	 *
	 * @param callable $callback
	 */
	public function each(callable $callback): void {
		/**
		 * @var string $key
		 * @var mixed $value
		 */
		foreach ($this->array as $key => $value) {
			$callback($key, $value);
		}
	}

	/**
	 * Searches the collection with a given callback and returns the key for the first element if found.
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
	 * @return mixed|null the key or null if it hasn't been found
	 */
	public function findKey(mixed ...$arguments) {
		/** @var mixed $index */
		$index = count($arguments) === 1 ? $this->find($arguments[0]) : $this->find($arguments[0], $arguments[1]);

		return $this->getKey($index);
	}

	/**
	 * Searches the collection with a given callback and returns the key for the last element if found.
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
	 * @return mixed|null the key or null if it hasn't been found
	 */
	public function findLastKey(mixed ...$arguments) {
		/** @var mixed $index */
		$index = count($arguments) === 1 ? $this->findLast($arguments[0]) : $this->findLast($arguments[0], $arguments[1]);

		return $this->getKey($index);
	}

	/**
	 * @param mixed $offset
	 * @param mixed $value
	 *
	 * @internal
	 */
	public function offsetSet(mixed $offset, mixed $value): void {
		/** @var string|null $offset */
		if ($offset !== null) {
			$this->array[$offset] = $value;
		}
	}

	/**
	 * @param mixed $offset
	 *
	 * @return bool
	 *
	 * @internal
	 */
	public function offsetExists(mixed $offset): bool {
		/** @var string $offset */
		return isset($this->array[$offset]);
	}

	/**
	 * @param mixed $offset
	 *
	 * @internal
	 */
	public function offsetUnset(mixed $offset): void {
		/** @var string $offset */
		unset($this->array[$offset]);
	}

	/**
	 * @param mixed $offset
	 *
	 * @return mixed
	 *
	 * @internal
	 */
	public function offsetGet(mixed $offset): mixed {
		/** @var string $offset */
		return isset($this->array[$offset]) ? $this->array[$offset] : null;
	}
}
