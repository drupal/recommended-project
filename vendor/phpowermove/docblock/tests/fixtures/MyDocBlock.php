<?php declare(strict_types=1);
namespace phpowermove\docblock\tests\fixtures;

use phpowermove\docblock\Docblock;

class MyDocBlock extends Docblock {
	protected function splitDocblock($comment): array {
		return ['', '', 'Invalid tag block'];
	}
}
