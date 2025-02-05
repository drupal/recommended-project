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

use phootwork\lang\Comparator;

trait SortAssocPart {
	/**
	 * Sorts the array and persisting key-value pairs
	 *
	 * @param Comparator|callable|null $cmp
	 *
	 * @return $this
	 */
	public function sortAssoc(Comparator|callable|null $cmp = null): self {
		$this->doSort($this->array, 'uasort', 'asort', $cmp);

		return $this;
	}

	/**
	 * Sorts the array by keys
	 *
	 * @param Comparator|callable|null $cmp
	 *
	 * @return $this
	 */
	public function sortKeys(Comparator|callable|null $cmp = null): self {
		$this->doSort($this->array, 'uksort', 'ksort', $cmp);

		return $this;
	}
}
