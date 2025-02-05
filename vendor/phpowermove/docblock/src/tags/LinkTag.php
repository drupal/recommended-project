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
 * Represents a `@link` tag
 * 
 * @see https://docs.phpdoc.org/3.0/guide/references/phpdoc/tags/link.html
 */
class LinkTag extends AbstractDescriptionTag {
	private string $url = '';

	/**
	 * Url Regex by @diegoperini
	 * 
	 * @see https://mathiasbynens.be/demo/url-regex
	 *
	 * @var string
	 */
	public const URL_REGEX = '_^(?:(?:https?|ftp)://)(?:\S+(?::\S*)?@)?(?:(?!10(?:\.\d{1,3}){3})(?!127(?:\.\d{1,3}){3})(?!169\.254(?:\.\d{1,3}){2})(?!192\.168(?:\.\d{1,3}){2})(?!172\.(?:1[6-9]|2\d|3[0-1])(?:\.\d{1,3}){2})(?:[1-9]\d?|1\d\d|2[01]\d|22[0-3])(?:\.(?:1?\d{1,2}|2[0-4]\d|25[0-5])){2}(?:\.(?:[1-9]\d?|1\d\d|2[0-4]\d|25[0-4]))|(?:(?:[a-z\x{00a1}-\x{ffff}0-9]+-?)*[a-z\x{00a1}-\x{ffff}0-9]+)(?:\.(?:[a-z\x{00a1}-\x{ffff}0-9]+-?)*[a-z\x{00a1}-\x{ffff}0-9]+)*(?:\.(?:[a-z\x{00a1}-\x{ffff}]{2,})))(?::\d{2,5})?(?:/[^\s]*)?$_iuS';

	protected function parse(string $content): void {
		$parts = preg_split('/\s+/Su', $content, 2);

		$urlCandidate = $parts[0];
		if (preg_match(self::URL_REGEX, $urlCandidate)) {
			$this->url = $urlCandidate;
			$this->setDescription($parts[1] ?? '');
		} else {
			$this->setDescription($content);
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

	public function toString(): string {
		return trim(sprintf('@link %s', trim($this->url . ' ' . $this->description)));
	}
}
