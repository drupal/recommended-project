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

interface Comparable {
	/**
	 * Compares to another object
	 *
	 * @param mixed $comparison
	 *
	 * @return int Return Values:<br>
	 * 		&lt; 0 if the object is less than comparison<br>
	 *  	&gt; 0 if the object is greater than comparison<br>
	 * 		0 if they are equal.
	 */
	public function compareTo(mixed $comparison): int;
}
