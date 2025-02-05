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

trait EachPart {
	/**
	 * Iterates the array and calls the callback function with the current item as parameter
	 *
	 * @param callable $callback
	 */
	public function each(callable $callback): void {
		array_map($callback, $this->array);
	}
}
