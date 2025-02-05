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

trait AccessorsPart {
	/**
	 * Returns the element at the given index (or null if the index isn't present)
	 *
	 * @param int|string $index
	 *
	 * @return mixed
	 */
	public function get(int|string $index): mixed {
		return $this->array[$index] ?? null;
	}
}
