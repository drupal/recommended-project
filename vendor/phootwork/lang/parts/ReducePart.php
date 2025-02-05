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

trait ReducePart {
	/**
	 * Iterative reduction of this array or collection with the help of a callback function. The callback
	 * function takes two parameters, the first is the carry, the second the current item, with this
	 * signature: mixed callback(mixed $carry, mixed $item)
	 *
	 * @param callable $callback the callback function
	 * @param mixed $fallback the default value, that will be returned when the list is empty
	 *
	 * @return mixed
	 *
	 * @psalm-suppress MixedArgumentTypeCoercion $callback is a callback
	 */
	public function reduce(callable $callback, mixed $fallback = null): mixed {
		return array_reduce($this->array, $callback, $fallback);
	}
}
