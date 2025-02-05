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
 * Represents a Set
 *
 * @author Thomas Gossmann
 */
class Set extends AbstractList {
	/**
	 * Creates a new Set
	 *
	 * @param array|Iterator $collection
	 */
	public function __construct(array|Iterator $collection = []) {
		$this->add(...$collection);
	}

	/**
	 * Adds an element to that set
	 *
	 * @param mixed ...$elements
	 *
	 * @return $this
	 */
	public function add(mixed ...$elements): self {
		/** @var mixed $element */
		foreach ($elements as $element) {
			if (!in_array($element, $this->array, true)) {
				$this->array[$this->size()] = $element;
			}
		}

		return $this;
	}
}
