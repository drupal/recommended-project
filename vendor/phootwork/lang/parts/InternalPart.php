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

use InvalidArgumentException;

/**
 * Internal Text methods
 *
 * @author Thomas Gossmann
 * @author Cristiano Cinotti
 */
trait InternalPart {
	abstract public function length(): int;

	/**
	 * @internal
	 *
	 * @param int $offset
	 *
	 * @return int
	 */
	protected function prepareOffset(int $offset): int {
		$len = $this->length();
		if ($offset < -$len || $offset > $len) {
			throw new InvalidArgumentException("Offset must be in range [-$len, $len]");
		}

		if ($offset < 0) {
			$offset += $len;
		}

		return $offset;
	}

	/**
	 * @param int      $offset
	 * @param int|null $length
	 *
	 * @return int
	 *
	 * @internal
	 *
	 */
	protected function prepareLength(int $offset, ?int $length): int {
		$length = (null === $length) ? ($this->length() - $offset) : (
			($length < 0) ? ($length + $this->length() - $offset) : $length
		);

		if ($length < 0) {
			throw new InvalidArgumentException('Length too small');
		}

		if ($offset + $length > $this->length()) {
			throw new InvalidArgumentException('Length too large');
		}

		return $length;
	}
}
