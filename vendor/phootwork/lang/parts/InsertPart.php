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

trait InsertPart {
	abstract public function add(mixed ...$elements);

	/**
	 * Insert one element at the given index
	 *
	 * @param mixed $element
	 * @param int|null|string $index
	 *
	 * @return $this
	 */
	public function insert(mixed $element, int|string|null $index): self {
		if (null === $index) {
			return $this->add($element);
		}

		is_int($index) ? array_splice($this->array, $index, 0, [$element])
			: $this->array[$index] = $element;

		return $this;
	}
}
