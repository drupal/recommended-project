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
 * Represents the `@see' tag.
 * 
 * @see https://docs.phpdoc.org/3.0/guide/references/phpdoc/tags/see.html
 */
class SeeTag extends AbstractDescriptionTag {
	protected string $reference;

	protected function parse(string $content): void {
		$parts = preg_split('/\s+/Su', $content, 2);

		$this->reference = $parts[0];
		$this->setDescription(isset($parts[1]) ? $parts[1] : '');
	}

	public function toString(): string {
		return trim(sprintf('@see %s', trim($this->reference . ' ' . $this->description)));
	}

	/**
	 * Returns the reference
	 * 
	 * @return string the reference
	 */
	public function getReference(): string {
		return $this->reference;
	}

	/**
	 * Sets the reference
	 *
	 * @param string $reference a URL or FQSEN
	 *
	 * @return SeeTag
	 */
	public function setReference(string $reference): self {
		$this->reference = $reference;

		return $this;
	}
}
