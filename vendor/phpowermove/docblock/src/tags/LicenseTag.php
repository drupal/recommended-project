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
 * Represents a `@license` tag.
 * 
 * @see https://docs.phpdoc.org/3.0/guide/references/phpdoc/tags/license.html
 */
class LicenseTag extends AbstractTag {
	private string $url = '';
	private string $license = '';

	protected function parse(string $content): void {
		$parts = preg_split('/\s+/Su', $content, 2);

		$urlCandidate = $parts[0];
		if (preg_match(LinkTag::URL_REGEX, $urlCandidate)) {
			$this->url = $urlCandidate;
			$this->license = $parts[1] ?? '';
		} else {
			$this->license = $content;
		}
	}

	/**
	 * Returns the url
	 *
	 * @return string the url
	 */
	public function getUrl(): string {
		return $this->url;
	}

	/**
	 * Sets the url
	 *
	 * @param string $url
	 *
	 * @return $this
	 */
	public function setUrl(string $url): self {
		$this->url = $url;

		return $this;
	}

	/**
	 * Returns the license
	 * 
	 * @return string
	 */
	public function getLicense(): string {
		return $this->license;
	}

	/**
	 * Sets the license
	 * 
	 * @param string $license
	 *
	 * @return $this        	
	 */
	public function setLicense(string $license): self {
		$this->license = $license;

		return $this;
	}

	public function toString(): string {
		return sprintf('@license %s', trim($this->url . ' ' . $this->license));
	}
}
