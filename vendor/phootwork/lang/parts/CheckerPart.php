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

use phootwork\lang\inflector\Inflector;
use phootwork\lang\inflector\InflectorInterface;

trait CheckerPart {
	abstract protected function getString(): string;

	/**
	 * Checks if the string is empty
	 *
	 * @return bool
	 */
	public function isEmpty(): bool {
		return empty($this->getString());
	}

	/**
	 * Check if the string contains only alphanumeric characters.
	 *
	 * @return bool
	 */
	public function isAlphanumeric(): bool {
		return ctype_alnum($this->getString());
	}

	/**
	 * Check if the string contains only alphanumeric characters.
	 *
	 * @return bool
	 */
	public function isAlphabetic(): bool {
		return ctype_alpha($this->getString());
	}

	/**
	 * Check if the string contains only numeric characters.
	 *
	 * @return bool
	 */
	public function isNumeric(): bool {
		return ctype_digit($this->getString());
	}

	/**
	 * Check if the string contains only characters which are not whitespace or an alphanumeric.
	 *
	 * @return bool
	 */
	public function isPunctuation(): bool {
		return ctype_punct($this->getString());
	}

	/**
	 * Check if the string contains only space characters.
	 *
	 * @return bool
	 */
	public function isSpace(): bool {
		return ctype_space($this->getString());
	}

	/**
	 * Check if the string contains only lower case characters.
	 *
	 * Spaces are considered non-lowercase characters, so lowercase strings with multiple words, separated by spaces,
	 * return false. E.g.:
	 *
	 * <code>
	 * $text = new Text('lowercase multi words string');<br>
	 * var_dump($text->isLowercase()); // false
	 * </code>
	 *
	 * @return bool
	 */
	public function isLowerCase(): bool {
		return ctype_lower($this->getString());
	}

	/**
	 * Check if the string contains only upper case characters.
	 *
	 * Spaces are considered non-uppercase characters, so uppercase strings with multiple words, separated by spaces,
	 * return false. E.g.:
	 *
	 * <code>
	 * $text = new Text('UPPERCASE MULTI WORDS STRING'); <br>
	 * var_dump($text->isUppercase()); // false
	 * </code>
	 *
	 * @return bool
	 */
	public function isUpperCase(): bool {
		return ctype_upper($this->getString());
	}

	/**
	 * Check if a string is singular form.
	 *
	 * @param InflectorInterface|null $pluralizer
	 *            A custom pluralizer. Default is the Inflector
	 *
	 * @return bool
	 */
	public function isSingular(?InflectorInterface $pluralizer = null): bool {
		$pluralizer = $pluralizer ?? new Inflector();

		return $pluralizer->isSingular($this->getString());
	}

	/**
	 * Check if a string is plural form.
	 *
	 * @param InflectorInterface|null $pluralizer
	 *            A custom pluralizer. Default is the Inflector
	 *
	 * @return bool
	 */
	public function isPlural(?InflectorInterface $pluralizer = null): bool {
		$pluralizer = $pluralizer ?? new Inflector();

		return $pluralizer->isPlural($this->getString());
	}
}
