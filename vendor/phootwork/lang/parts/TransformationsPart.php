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

use phootwork\lang\Arrayable;
use phootwork\lang\ArrayObject;
use phootwork\lang\inflector\Inflector;
use phootwork\lang\inflector\InflectorInterface;
use phootwork\lang\Text;
use Stringable;

/**
 * Text transformation methods
 *
 * @author Thomas Gossmann
 * @author Cristiano Cinotti
 */
trait TransformationsPart {
	abstract protected function getString(): string;

	/**
	 * Slices a piece of the string from a given start to an end.
	 * If no length is given, the String is sliced to its maximum length.
	 *
	 * @param int $start
	 * @param int $end
	 *
	 * @return Text
	 */
	abstract public function substring(int $start, ?int $end = null): Text;

	/**
	 * Splits the string by string
	 *
	 * @param string $delimiter
	 * @param int    $limit
	 *
	 * @return ArrayObject
	 */
	abstract public function split(string $delimiter, int $limit = PHP_INT_MAX): ArrayObject;

	/**
	 * Strip whitespace (or other characters) from the beginning and end of the string
	 *
	 * @param string $characters
	 *
	 * @return Text
	 */
	abstract public function trim(string $characters): Text;

	/**
	 * @param string|Stringable $text
	 *
	 * @return bool
	 */
	abstract public function contains(Stringable|string $text): bool;

	/**
	 * Replace all occurrences of the search string with the replacement string
	 *
	 * @param Arrayable|Stringable|array|string $search
	 *        The value being searched for, otherwise known as the needle. An array may be used
	 *        to designate multiple needles.
	 * @param Arrayable|Stringable|array|string $replace
	 *        The replacement value that replaces found search values. An array may be used to
	 *        designate multiple replacements.
	 *
	 * @return Text
	 */
	abstract public function replace(Arrayable|Stringable|array|string $search, Arrayable|Stringable|array|string $replace): Text;

	/**
	 * Transforms the string to lowercase
	 *
	 * @return Text
	 */
	public function toLowerCase(): Text {
		return new Text(mb_strtolower($this->getString(), $this->encoding), $this->encoding);
	}

	/**
	 * Transforms the string to first character lowercased
	 *
	 * @return Text
	 */
	public function toLowerCaseFirst(): Text {
		$first = $this->substring(0, 1);
		$rest = $this->substring(1);

		return new Text(mb_strtolower((string) $first, $this->encoding) . $rest, $this->encoding);
	}

	/**
	 * Transforms the string to uppercase
	 *
	 * @return Text
	 */
	public function toUpperCase(): Text {
		return new Text(mb_strtoupper($this->getString(), $this->encoding), $this->encoding);
	}

	/**
	 * Transforms the string to first character uppercased
	 *
	 * @return Text
	 */
	public function toUpperCaseFirst(): Text {
		$first = $this->substring(0, 1);
		$rest = $this->substring(1);

		return new Text(mb_strtoupper((string) $first, $this->encoding) . $rest, $this->encoding);
	}

	/**
	 * Transforms the string to only its first character capitalized.
	 *
	 * @return Text
	 */
	public function toCapitalCase(): Text {
		return $this->toLowerCase()->toUpperCaseFirst();
	}

	/**
	 * Transforms the string with the words capitalized.
	 *
	 * @return Text
	 */
	public function toCapitalCaseWords(): Text {
		$encoding = $this->encoding;

		return $this->split(' ')->map(function (string $str) use ($encoding) {
			return Text::create($str, $encoding)->toCapitalCase();
		})->join(' ');
	}

	/**
	 * Converts this string into camelCase. Numbers are considered as part of its previous piece.
	 *
	 * <code>
	 * $var = new Text('my_own_variable');<br>
	 * $var->toCamelCase(); // myOwnVariable
	 *
	 * $var = new Text('my_test3_variable');<br>
	 * $var->toCamelCase(); // myTest3Variable
	 * </code>
	 *
	 * @return Text
	 */
	public function toCamelCase(): Text {
		return $this->toStudlyCase()->toLowerCaseFirst();
	}

	/**
	 * Converts this string into snake_case. Numbers are considered as part of its previous piece.
	 *
	 * <code>
	 * $var = new Text('myOwnVariable');<br>
	 * $var->toSnakeCase(); // my_own_variable
	 *
	 * $var = new Text('myTest3Variable');<br>
	 * $var->toSnakeCase(); // my_test3_variable
	 * </code>
	 *
	 * @return Text
	 */
	public function toSnakeCase(): Text {
		return $this->toKebabCase()->replace('-', '_');
	}

	/**
	 * Converts this string into StudlyCase. Numbers are considered as part of its previous piece.
	 *
	 * <code>
	 * $var = new Text('my_own_variable');<br>
	 * $var->toStudlyCase(); // MyOwnVariable
	 *
	 * $var = new Text('my_test3_variable');<br>
	 * $var->toStudlyCase(); // MyTest3Variable
	 * </code>
	 *
	 * @return Text
	 *
	 * @psalm-suppress MixedArgument $matches[0] is a string
	 */
	public function toStudlyCase(): Text {
		$input = $this->trim('-_');
		if ($input->isEmpty()) {
			return $input;
		}
		$normString = preg_replace('/\s+/', ' ', $input->toString());
		$encoding = $this->encoding;

		return Text::create(preg_replace_callback(
			'/([A-Z-_\s][a-z0-9]+)/',
			fn (array $matches): string => ucfirst(str_replace(['-', '_', ' '], '', $matches[0])),
			$normString
		), $encoding)
			->toUpperCaseFirst();
	}

	/**
	 * Convert this string into kebab-case. Numbers are considered as part of its previous piece.
	 *
	 * <code>
	 * $var = new Text('myOwnVariable');<br>
	 * $var->toKebabCase(); // my-own-variable
	 *
	 * $var = new Text('myTest3Variable');<br>
	 * $var->toKebabCase(); // my-test3-variable
	 * </code>
	 *
	 * @return Text
	 */
	public function toKebabCase(): Text {
		$input = $this->trim('-_');
		$normString = str_replace([' ', '_'], '-', preg_replace('/\s+/', ' ', $input->toString()));

		return new Text(mb_strtolower(preg_replace('/([a-z0-9])([A-Z])/', '$1-$2', $normString)), $this->encoding);
	}

	/**
	 * Get the plural form of the Text object.
	 *
	 * @param InflectorInterface|null $pluralizer
	 *
	 * @return Text
	 */
	public function toPlural(?InflectorInterface $pluralizer = null): Text {
		$pluralizer = $pluralizer ?: new Inflector();

		return new Text($pluralizer->getPluralForm($this->getString()), $this->encoding);
	}

	/**
	 * Get the singular form of the Text object.
	 *
	 * @param InflectorInterface|null $pluralizer
	 *
	 * @return Text
	 */
	public function toSingular(?InflectorInterface $pluralizer = null): Text {
		$pluralizer = $pluralizer ?: new Inflector();

		return new Text($pluralizer->getSingularForm($this->getString()), $this->encoding);
	}

	/**
	 * Converts each tab in the string to some number of spaces, as defined by
	 * $tabLength. By default, each tab is converted to 4 consecutive spaces.
	 *
	 * @param int $tabLength Number of spaces to replace each tab with
	 *
	 * @return Text text with tabs converted to spaces
	 */
	public function toSpaces(int $tabLength = 4): Text {
		$spaces = str_repeat(' ', $tabLength);

		return $this->replace("\t", $spaces);
	}
	/**
	 * Converts each occurrence of some consecutive number of spaces, as
	 * defined by $tabLength, to a tab. By default, each 4 consecutive spaces
	 * are converted to a tab.
	 *
	 * @param int $tabLength Number of spaces to replace with a tab
	 *
	 * @return Text text with spaces converted to tabs
	 */
	public function toTabs(int $tabLength = 4): Text {
		$spaces = str_repeat(' ', $tabLength);

		return $this->replace($spaces, "\t");
	}
}
