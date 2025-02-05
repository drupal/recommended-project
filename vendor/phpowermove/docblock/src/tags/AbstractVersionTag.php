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
 * Represents tags which are in the format
 *
 *   `@tag [Version] [Description]`
 */
abstract class AbstractVersionTag extends AbstractDescriptionTag {

	/**
	 * PCRE regular expression matching a version vector.
	 * Assumes the "x" modifier.
	 */
	public const REGEX_VERSION = '(?:
        # Normal release vectors.
        \d\S*
        |
        # VCS version vectors. Per PHPCS, they are expected to
        # follow the form of the VCS name, followed by ":", followed
        # by the version vector itself.
        # By convention, popular VCSes like CVS, SVN and GIT use "$"
        # around the actual version vector.
        [^\s\:]+\:\s*\$[^\$]+\$
    )';

	protected string $version = '';

	/**
	 * @see https://github.com/phpDocumentor/ReflectionDocBlock/blob/master/src/phpDocumentor/Reflection/DocBlock/Tag/VersionTag.php Original Method: setContent()
	 * @see \phpowermove\docblock\tags\AbstractTag::parse()
	 *
	 * @param string $content
	 */
	protected function parse(string $content): void {
		$matches = [];
		if (preg_match(
			'/^
	                # The version vector
	                (' . self::REGEX_VERSION . ')
	                \s*
	                # The description
	                (.+)?
	            $/sux',
			$content,
			$matches
		)) {
			$this->version = $matches[1];
			$this->setDescription($matches[2] ?? '');
		}
	}

	public function toString(): string {
		return trim(sprintf('@%s %s %s', $this->tagName, $this->version, $this->description));
	}

	/**
	 * Returns the version
	 * 
	 * @return string the version
	 */
	public function getVersion(): string {
		return $this->version;
	}

	/**
	 * Sets the version
	 * 
	 * @param string $version the new version 
	 *
	 * @return $this       	
	 */
	public function setVersion(string $version): self {
		$this->version = $version;

		return $this;
	}
}
