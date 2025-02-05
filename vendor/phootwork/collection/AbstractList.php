<?php declare(strict_types=1);
/**
 * This file is part of the Phootwork package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @license MIT License
 * @copyright Thomas Gossmann
 */
namespace phootwork\collection;

use phootwork\lang\Comparator;
use phootwork\lang\parts\EachPart;
use phootwork\lang\parts\ReducePart;
use phootwork\lang\parts\RemovePart;
use phootwork\lang\parts\ReversePart;

/**
 * Abstract class for all list-like collections
 *
 * @author Thomas Gossmann
 * @author Cristiano Cinotti
 */
abstract class AbstractList extends AbstractCollection {
	use EachPart;
	use ReducePart;
	use RemovePart;
	use ReversePart;

	/**
	 * Sorts the collection in reverse order
	 *
	 * @see #sort
	 * @see #reverse
	 *
	 * @param Comparator|callable|null $cmp
	 *
	 * @return $this
	 */
	public function reverseSort(Comparator|callable|null $cmp = null): self {
		return $this->sort($cmp)->reverse();
	}
}
