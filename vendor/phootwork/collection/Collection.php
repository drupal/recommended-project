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

/**
 * Collection interface
 *
 * @author Thomas Gossmann
 */
interface Collection extends \Iterator {
	/**
	 * Resets the collection
	 * 
	 * @return void
	 */
	public function clear(): void;

	/**
	 * Checks whether this collection is empty
	 * 
	 * @return bool
	 */
	public function isEmpty(): bool;

	/**
	 * Checks whether the given element is in this collection
	 * 
	 * @param mixed $element
	 *
	 * @return bool
	 */
	public function contains(mixed $element): bool;

	/**
	 * Returns the amount of elements in this collection
	 * 
	 * @return int
	 */
	public function size(): int;

	/**
	 * Returns the collection as an array
	 * 
	 * @return array
	 */
	public function toArray(): array;
}
