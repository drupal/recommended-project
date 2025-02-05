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

/**
 * Represents a Queue
 * 
 * FIFO - first in first out
 * 
 * @author Thomas Gossmann
 */
class Queue extends AbstractList {
	/**
	 * Creates a new Queue
	 * 
	 * @param array|Iterator $collection
	 */
	public function __construct(array|Iterator $collection = []) {
		$this->enqueue(...$collection);
	}

	/**
	 * Enqueues an element
	 * 
	 * @param mixed ...$elements
	 *
	 * @return $this
	 */
	public function enqueue(mixed ...$elements): self {
		array_unshift($this->array, ...$elements);

		return $this;
	}

	/**
	 * Returns the element at the head or null if the queue is empty but doesn't remove that element  
	 * 
	 * @return mixed
	 */
	public function peek(): mixed {
		if ($this->size() > 0) {
			return $this->array[0];
		}

		return null;
	}

	/**
	 * Removes and returns the element at the head or null if the is empty
	 * 
	 * @return mixed
	 */
	public function poll(): mixed {
		return array_shift($this->array);
	}
}
