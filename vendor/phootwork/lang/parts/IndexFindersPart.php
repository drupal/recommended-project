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

trait IndexFindersPart {
	abstract public function find(mixed ...$arguments);

	abstract public function findLast(mixed ...$arguments);

	/**
	 * Returns the index of the given element or null if the element can't be found
	 *
	 * @param mixed $element
	 *
	 * @return int|string|null the index for the given element
	 */
	public function indexOf(mixed $element): int|string|null {
		$out = array_search($element, $this->array, true);

		return $out === false ? null : $out;
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
	 * @return int|string|null the index or null if it hasn't been found
	 */
	public function findLastIndex(mixed ...$arguments): int|string|null {
		/** @var mixed $index */
		$index = count($arguments) === 1 ?
			$this->findLast($arguments[0]) : $this->findLast($arguments[0], $arguments[1]);

		return $this->indexOf($index);
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
	 * @return int|string|null the index or null if it hasn't been found
	 */
	public function findIndex(mixed ...$arguments): int|string|null {
		/** @var mixed $index */
		$index = count($arguments) === 1 ? $this->find($arguments[0]) : $this->find($arguments[0], $arguments[1]);

		return $this->indexOf($index);
	}
}
