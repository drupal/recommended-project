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

trait AddPart {
	/**
	 * Adds one or more elements to that array
	 *
	 * @param mixed ...$elements
	 *
	 * @return $this
	 */
	public function add(mixed ...$elements): self {
		/** @var mixed $element */
		foreach ($elements as $element) {
			$this->array[count($this->array)] = $element;
		}

		return $this;
	}
}
