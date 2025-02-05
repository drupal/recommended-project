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
use phootwork\lang\parts\AccessorsPart;
use phootwork\lang\parts\AddPart;
use phootwork\lang\parts\IndexFindersPart;
use phootwork\lang\parts\InsertPart;

/**
 * Represents a List
 * 
 * @author Thomas Gossmann
 * @author Cristiano Cinotti
 */
class ArrayList extends AbstractList {
	use AccessorsPart {
		get as traitGet;
	}
	use AddPart;
	use IndexFindersPart {
		indexOf as traitIndexOf;
		findLastIndex as traitFindLastIndex;
		findIndex as traitFindIndex;
	}
	use InsertPart;

	/**
	 * Creates a new ArrayList
	 * 
	 * @param array|Iterator $collection
	 */
	public function __construct(array|Iterator $collection = []) {
		/** @var mixed $element */
		foreach ($collection as $element) {
			$this->add($element);
		}
	}

	/**
	 * Removes an element from the list by its index.
	 *
	 * @param int $index
	 *
	 * @return ArrayList
	 */
	public function removeByIndex(int $index): self {
		if (isset($this->array[$index])) {
			unset($this->array[$index]);

			if (!array_is_list($this->array)) {
				$this->array = array_values($this->array);
			}
		}

		return $this;
	}

	/**
	 * Returns the element at the given index (or null if the index isn't present)
	 *
	 * @param int $index
	 *
	 * @return mixed
	 */
	public function get(int $index): mixed {
		return $this->traitGet($index);
	}

	/**
	 * Returns the index of the given element or null if the element can't be found
	 *
	 * @param mixed $element
	 *
	 * @return int|null the index for the given element
	 */
	public function indexOf(mixed $element): ?int {
		$index = $this->traitIndexOf($element);

		return $index === null ? $index : (int) $index;
	}

	/**
	 * Searches the array with a given callback and returns the index for the last element if found.
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
	 * @return int|null the index or null if it hasn't been found
	 */
	public function findLastIndex(mixed ...$arguments): ?int {
		$lastIndex = $this->traitFindLastIndex(...$arguments);

		return $lastIndex === null ? $lastIndex : (int) $lastIndex;
	}

	/**
	 * Searches the array with a given callback and returns the index for the first element if found.
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
	 * @return int|null the index or null if it hasn't been found
	 */
	public function findIndex(mixed ...$arguments): ?int {
		$index = $this->traitFindIndex(...$arguments);

		return $index === null ? $index : (int) $index;
	}
}
