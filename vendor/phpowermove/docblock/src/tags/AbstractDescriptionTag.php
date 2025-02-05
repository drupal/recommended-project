<?php declare(strict_types=1);
/*
 * This file is part of the Docblock package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @license MIT License
 */

namespace phpowermove\docblock\tags;

/**
 * Abstract tag with a description
 */
abstract class AbstractDescriptionTag extends AbstractTag {
	protected string $description = '';

	/**
	 * Returns the description
	 * 
	 * @return string the description
	 */
	public function getDescription(): string {
		return $this->description;
	}

	/**
	 * Sets the description
	 * 
	 * @param string $description the new description
	 *
	 * @return $this
	 */
	public function setDescription(string $description): self {
		$this->description = $description;

		return $this;
	}
}
