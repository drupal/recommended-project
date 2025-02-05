<?php declare(strict_types=1);
/*
 * This file is part of the Docblock package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @license MIT License
 */

namespace phpowermove\docblock;

use phootwork\lang\Comparator;

class TagNameComparator implements Comparator {
	public function compare($a, $b): int {
		$order = ['see', 'author', 'property-read', 'property-write', 'property',
				'method', 'deprecated', 'since', 'version', 'var', 'type', 'param',
				'throws', 'return'];

		if ($a == $b) {
			return 0;
		}

		if (!in_array($a, $order)) {
			return -1;
		}

		if (!in_array($b, $order)) {
			return 1;
		}

		$pos1 = array_search($a, $order);
		$pos2 = array_search($b, $order);

		return $pos1 < $pos2 ? -1 : 1;
	}
}
