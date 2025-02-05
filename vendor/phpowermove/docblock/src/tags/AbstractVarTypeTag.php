<?php declare(strict_types=1);
/*
 * This file is part of the Docblock package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @license MIT License
 */

namespace phpowermove\docblock\tags;

use phootwork\lang\Text;

/**
 * Represents tags which are in the format
 *
 *   @tag [Type] [Variable] [Description]
 */
abstract class AbstractVarTypeTag extends AbstractTypeTag {
	protected string $variable = '';
	protected bool $isVariadic = false;

	/**
	 * @see https://github.com/phpDocumentor/ReflectionDocBlock/blob/master/src/phpDocumentor/Reflection/DocBlock/Tag/ParamTag.php Original Method: setContent()
	 * @see \phpowermove\docblock\tags\AbstractTypeTag::parse()
	 *
	 * @param string $content
	 */
	protected function parse(string $content): void {
		$parts = preg_split('/(\s+)/Su', $content, 3, PREG_SPLIT_DELIM_CAPTURE);

		$this->parseType($parts);
		$this->parseVariable($parts);
		$this->setDescription(implode('', $parts));
	}

	/**
	 * Parses the type from the extracted parts
	 * 
	 * @param string[] $parts
	 */
	private function parseType(array &$parts): void {
		// if the first item that is encountered is not a variable; it is a type
		if (isset($parts[0])
				&& (strlen($parts[0]) > 0)
				&& !str_starts_with($parts[0], '$')
				&& !str_starts_with($parts[0], '...$')) {
			$this->type = array_shift($parts);
			array_shift($parts);
		}
	}

	/**
	 * Parses the variable from the extracted parts
	 *
	 * @param string[] $parts
	 */
	private function parseVariable(array &$parts): void {
		// if the next item starts with a $ or ...$ it must be the variable name
		if (isset($parts[0])
				&& (strlen($parts[0]) > 0)
				&& (str_starts_with($parts[0], '$') || str_starts_with($parts[0], '...$'))) {
			$this->variable = array_shift($parts);
			array_shift($parts);

			if (str_starts_with($this->variable, '...')) {
				$this->isVariadic = true;
				$this->variable = substr($this->variable, 3);
			}
		}
	}

	public function toString(): string {
		$type = $this->type === '' ? '' : $this->type . ' ';
		$var = $this->variable !== ''
			? ($this->isVariadic ? '...' : '') . $this->variable . ' ' : '';

		return trim(sprintf('@%s %s%s%s', $this->tagName, $type, $var, $this->description));
	}

	/**
	 * Returns the variable name, starting with `$`
	 * 
	 * @return string the variable name
	 */
	public function getExpression(): string {
		return $this->variable;
	}

	/**
	 * Sets the variable name
	 *
	 * @param string $variable the new variable name
	 *
	 * @return $this        	
	 */
	public function setVariable(string $variable): self {
		if (str_starts_with($variable, '...')) {
			$this->setVariadic(true);
			$variable = substr($variable, 3);
		}

		$this->variable = str_starts_with($variable, '$') ? $variable : "\$$variable";

		return $this;
	}

	/**
	 * Returns the variable name
	 *
	 * @return string the variable name
	 */
	public function getVariable(): string {
		$variable = new Text($this->variable);

		return $variable->isEmpty() ? '' : $variable->slice(1)->toString();
	}

	/**
	 * Returns if the variable is variadic
	 * 
	 * @return bool if the variable is variadic
	 */
	public function isVariadic(): bool {
		return $this->isVariadic;
	}

	/**
	 * Sets whether the variable should be variadic
	 * 
	 * @param bool $variadic
	 *
	 * @return $this        	
	 */
	public function setVariadic(bool $variadic): self {
		$this->isVariadic = $variadic;

		return $this;
	}
}
