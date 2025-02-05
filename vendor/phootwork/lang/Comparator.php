<?php declare(strict_types=1);
/**
 * This file is part of the Phootwork package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @license MIT License
 * @copyright Thomas Gossmann
 */
namespace phootwork\lang;

interface Comparator {
	/**
	 * Compares two objects
	 *
	 * @param mixed $a
	 * @param mixed $b
	 *
	 * @return int
	 * 		Return Values:
	 * 		< 0 if the $a is less than $b
	 * 		> 0 if the $a is greater than $b
	 * 		0 if they are equal.
	 */
	public function compare(mixed $a, mixed $b): int;
}
