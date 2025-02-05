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

trait ReversePart {
	/**
	 * Reverses the order of all elements
	 *
	 * @return $this
	 */
	public function reverse(): self {
		$this->array = array_reverse($this->array);

		return $this;
	}
}
