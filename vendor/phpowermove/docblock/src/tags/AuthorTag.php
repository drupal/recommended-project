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
 * Represents the `@author` tag.
 * 
 * @see https://docs.phpdoc.org/3.0/guide/references/phpdoc/tags/author.html
 */
class AuthorTag extends AbstractTag {

	/**
	 * PCRE regular expression matching any valid value for the name component.
	 */
	public const REGEX_AUTHOR_NAME = '[^\<]*';

	/**
	 * PCRE regular expression matching any valid value for the email component.
	 */
	public const REGEX_AUTHOR_EMAIL = '[^\>]*';

	protected string $name = '';
	protected string $email = '';

	/**
	 * @see https://github.com/phpDocumentor/ReflectionDocBlock/blob/master/src/phpDocumentor/Reflection/DocBlock/Tag/AuthorTag.php Original Method: setContent()
	 * @see \phpowermove\docblock\tags\AbstractTag::parse()
	 *
	 * @param string $content
	 */
	protected function parse(string $content): void {
		$matches = [];
		if (preg_match(
			'/^(' . self::REGEX_AUTHOR_NAME . ')(\<(' . self::REGEX_AUTHOR_EMAIL . ')\>)?$/u',
			$content,
			$matches
		)) {
			$this->name = trim($matches[1]);
			if (isset($matches[3])) {
				$this->email = trim($matches[3]);
			}
		}
	}

	public function toString(): string {
		$email = $this->email !== '' ? '<' . $this->email . '>' : '';

		return trim(sprintf('@author %s %s', $this->name, $email));
	}

	/**
	 * Returns the authors name
	 * 
	 * @return string the authors name
	 */
	public function getName(): string {
		return $this->name;
	}

	/**
	 * Sets the authors name
	 *
	 * @param string $name the new name
	 *
	 * @return $this     	
	 */
	public function setName(string $name): self {
		$this->name = $name;

		return $this;
	}

	/**
	 * Returns the authors email
	 * 
	 * @return string the authors email
	 */
	public function getEmail(): string {
		return $this->email;
	}

	/**
	 * Sets the authors email
	 * 
	 * @param string $email the new email
	 *
	 * @return $this         	
	 */
	public function setEmail(string $email): self {
		$this->email = $email;

		return $this;
	}
}
