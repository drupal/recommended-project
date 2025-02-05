<?php declare(strict_types=1);
/*
 * This file is part of the Docblock package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @license MIT License
 */

namespace phpowermove\docblock;

use InvalidArgumentException;
use LogicException;
use phootwork\collection\ArrayList;
use phootwork\collection\Map;
use phootwork\lang\Comparator;
use phpowermove\docblock\tags\AbstractTag;
use phpowermove\docblock\tags\TagFactory;
use ReflectionClass;
use ReflectionFunctionAbstract;
use ReflectionProperty;

class Docblock implements \Stringable {
	protected string $shortDescription;
	protected string $longDescription;
	protected ArrayList $tags;
	protected ?Comparator $comparator = null;

	public const REGEX_TAGNAME = '[\w\-\_\\\\]+';

	/**
	 * Static docblock factory
	 * 
	 * @param ReflectionFunctionAbstract|ReflectionClass|ReflectionProperty|string $docblock a docblock to parse
	 *
	 * @return $this
	 */
	public static function create(ReflectionFunctionAbstract|ReflectionClass|ReflectionProperty|string $docblock = ''): self {
		return new static($docblock);
	}

	/**
	 * Creates a new docblock instance and parses the initial string or reflector object if given
	 * 
	 * @param ReflectionFunctionAbstract|ReflectionClass|ReflectionProperty|string $docblock a docblock to parse
	 */
	final public function __construct(ReflectionFunctionAbstract|ReflectionClass|ReflectionProperty|string $docblock = '') {
		$this->tags = new ArrayList();
		$this->parse($docblock);
	}

	/**
	 * @see https://github.com/phpDocumentor/ReflectionDocBlock/blob/master/src/phpDocumentor/Reflection/DocBlock.php Original Method
	 *
	 * @param ReflectionFunctionAbstract|ReflectionClass|ReflectionProperty|string $docblock
	 *
	 * @throws InvalidArgumentException if there is no getDocComment() method available
	 */
	protected function parse(ReflectionFunctionAbstract|ReflectionClass|ReflectionProperty|string $docblock): void {
		$docblock = is_object($docblock) ? $docblock->getDocComment() : $docblock;
		$docblock = $this->cleanInput($docblock);

		[$short, $long, $tags] = $this->splitDocBlock($docblock);
		$this->shortDescription = $short;
		$this->longDescription = $long;
		$this->parseTags($tags);
	}

	/**
	 * Strips the asterisks from the DocBlock comment.
	 * 
	 * @see https://github.com/phpDocumentor/ReflectionDocBlock/blob/master/src/phpDocumentor/Reflection/DocBlock.php Original Method
	 *
	 * @param string $comment String containing the comment text.
	 *
	 * @return string
	 */
	protected function cleanInput(string $comment): string {
		$comment = trim(preg_replace('#[ \t]*(?:\/\*\*|\*\/|\*)?[ \t]{0,1}(.*)?#u', '$1', $comment));

		// reg ex above is not able to remove */ from a single line docblock
		if (substr($comment, -2) == '*/') {
			$comment = trim(substr($comment, 0, -2));
		}

		// normalize strings
		$comment = str_replace(["\r\n", "\r"], "\n", $comment);

		return $comment;
	}

	/**
	 * Splits the Docblock into a short description, long description and
	 * block of tags.
	 * 
	 * @see https://github.com/phpDocumentor/ReflectionDocBlock/blob/master/src/phpDocumentor/Reflection/DocBlock.php Original Method
	 *
	 * @param string $comment Comment to split into the sub-parts.
	 *
	 * @author RichardJ Special thanks to RichardJ for the regex responsible
	 *     for the split.
	 *
	 * @return string[] containing the short-, long description and an element
	 *     containing the tags.
	 */
	protected function splitDocBlock(string $comment): array {
		$matches = [];

		if (str_starts_with($comment, '@')) {
			$matches = ['', '', $comment];
		} else {
			// clears all extra horizontal whitespace from the line endings
			// to prevent parsing issues
			$comment = preg_replace('/\h*$/Sum', '', $comment);

			/*
			 * Splits the docblock into a short description, long description and
			 * tags section
			 * - The short description is started from the first character until
			 *   a dot is encountered followed by a newline OR
			 *   two consecutive newlines (horizontal whitespace is taken into
			 *   account to consider spacing errors)
			 * - The long description, any character until a new line is
			 *   encountered followed by an @ and word characters (a tag).
			 *   This is optional.
			 * - Tags; the remaining characters
			 *
			 * Big thanks to RichardJ for contributing this Regular Expression
			 */
			preg_match(
				'/
		        \A (
		          [^\n.]+
		          (?:
		            (?! \. \n | \n{2} ) # disallow the first seperator here
		            [\n.] (?! [ \t]* @\pL ) # disallow second seperator
		            [^\n.]+
		          )*
		          \.?
		        )
		        (?:
		          \s* # first seperator (actually newlines but it\'s all whitespace)
		          (?! @\pL ) # disallow the rest, to make sure this one doesn\'t match,
		          #if it doesn\'t exist
		          (
		            [^\n]+
		            (?: \n+
		              (?! [ \t]* @\pL ) # disallow second seperator (@param)
		              [^\n]+
		            )*
		          )
		        )?
		        (\s+ [\s\S]*)? # everything that follows
		        /ux',
				$comment,
				$matches
			);
			array_shift($matches);
		}

		while (count($matches) < 3) {
			$matches[] = '';
		}

		return $matches;
	}

	/**
	 * Parses the tags
	 * 
	 * @see https://github.com/phpDocumentor/ReflectionDocBlock/blob/master/src/phpDocumentor/Reflection/DocBlock.php Original Method
	 *
	 * @param string $tags
	 *
	 * @throws LogicException
	 * @throws InvalidArgumentException
	 */
	protected function parseTags(string $tags): void {
		$tags = trim($tags);
		if ($tags !== '') {

			// sanitize lines
			$result = [];
			foreach (explode("\n", $tags) as $line) {
				if ($this->isTagLine($line) || count($result) == 0) {
					$result[] = $line;
				} elseif ($line !== '') {
					$result[count($result) - 1] .= "\n" . $line;
				}
			}

			// create proper Tag objects
			if (count($result)) {
				$this->tags->clear();
				foreach ($result as $line) {
					$this->tags->add($this->parseTag($line));
				}
			}
		}
	}

	/**
	 * Checks whether the given line is a tag line (= starts with @) or not
	 * 
	 * @param string $line
	 *
	 * @return bool
	 */
	protected function isTagLine(string $line): bool {
		return str_starts_with($line, '@');
	}

	/**
	 * Parses an individual tag line
	 * 
	 * @param string $line
	 *
	 * @throws InvalidArgumentException
	 *
	 * @return AbstractTag
	 */
	protected function parseTag(string $line): AbstractTag {
		$matches = [];
		if (!preg_match('/^@(' . self::REGEX_TAGNAME . ')(?:\s*([^\s].*)|$)?/us', $line, $matches)) {
			throw new InvalidArgumentException('Invalid tag line detected: ' . $line);
		}

		$tagName = $matches[1];
		$content = $matches[2] ?? '';

		return TagFactory::create($tagName, $content);
	}

	/**
	 * Returns the short description
	 * 
	 * @return string the short description
	 */
	public function getShortDescription(): string {
		return $this->shortDescription;
	}

	/**
	 * Sets the short description
	 * 
	 * @param string $description the new description     
	 *
	 * @return $this   	
	 */
	public function setShortDescription(string $description = ''): self {
		$this->shortDescription = $description;

		return $this;
	}

	/**
	 * Returns the long description
	 *
	 * @return string the long description
	 */
	public function getLongDescription(): string {
		return $this->longDescription;
	}

	/**
	 * Sets the long description
	 * 
	 * @param string $description the new description
	 *
	 * @return $this
	 */
	public function setLongDescription(string $description = ''): self {
		$this->longDescription = $description;

		return $this;
	}

	/**
	 * Adds a tag to this docblock
	 * 
	 * @param AbstractTag $tag
	 *
	 * @return $this
	 */
	public function appendTag(AbstractTag $tag): self {
		$this->tags->add($tag);

		return $this;
	}

	/**
	 * Removes tags (by tag name)
	 *
	 * @param string $tagName
	 */
	public function removeTags(string $tagName = ''): void {
		$this->tags = $this->tags->filter(function (AbstractTag $tag) use ($tagName): bool {
			return $tagName !== $tag->getTagName();
		});
	}

	/**
	 * Checks whether a tag is present
	 * 
	 * @param string $tagName
	 *
	 * @return bool
	 */
	public function hasTag(string $tagName): bool {
		return $this->tags->search(
			$tagName,
			fn (AbstractTag $tag, string $query): bool => $tag->getTagName() === $query
		);
	}

	/**
	 * Gets tags (by tag name)
	 * 
	 * @param string $tagName
	 *
	 * @return ArrayList the tags
	 */
	public function getTags(string $tagName = ''): ArrayList {
		return $tagName === '' ? $this->tags : $this->tags->filter(
			fn (AbstractTag $tag): bool => $tag->getTagName() === $tagName
		);
	}

	/**
	 * A list of tags sorted by tag-name
	 * 
	 * @return ArrayList
	 */
	public function getSortedTags(): ArrayList {
		$this->comparator = $this->comparator ?? new TagNameComparator();

		// 1) group by tag name
		$group = new Map();
		/** @var AbstractTag $tag */
		foreach ($this->tags->toArray() as $tag) {
			if (!$group->has($tag->getTagName())) {
				$group->set($tag->getTagName(), new ArrayList());
			}

			/** @var ArrayList $list */
			$list = $group->get($tag->getTagName());
			$list->add($tag);
		}

		// 2) Sort the group by tag name
		$group->sortKeys(new TagNameComparator());

		// 3) flatten the group
		$sorted = new ArrayList();
		/** @var array $tags */
		foreach ($group->values()->toArray() as $tags) {
			$sorted->add(...$tags);
		}

		return $sorted;
	}

	/**
	 * Returns true when there is no content in the docblock
	 *  
	 * @return bool
	 */
	public function isEmpty(): bool {
		return $this->shortDescription === ''
				&& $this->longDescription === ''
				&& $this->tags->size() === 0;
	}

	/**
	 * Returns the string version of the docblock
	 * 
	 * @return string
	 */
	public function toString(): string {
		$docblock = "/**\n";

		// short description
		$short = trim($this->shortDescription);
		if ($short !== '') {
			$docblock .= $this->writeLines(explode("\n", $short));
		}

		// short description
		$long = trim($this->longDescription);
		if ($long !== '') {
			$docblock .= $this->writeLines(explode("\n", $long), !empty($short));
		}

		// tags
		$tags = $this->getSortedTags()->map(function (AbstractTag $tag): string {
			return (string) $tag;
		});

		if (!$tags->isEmpty()) {
			/** @psalm-suppress MixedArgumentTypeCoercion */
			$docblock .= $this->writeLines($tags->toArray(), $short !== '' || $long !== '');
		}

		$docblock .= ' */';

		return $docblock;
	}

	/**
	 * Writes multiple lines with ' * ' prefixed for docblock
	 * 
	 * @param string[] $lines the lines to be written
	 * @param bool $newline if a new line should be added before
	 *
	 * @return string the lines as string
	 */
	protected function writeLines(array $lines, bool $newline = false): string {
		$docblock = '';
		if ($newline) {
			$docblock .= " *\n";
		}

		foreach ($lines as $line) {
			if (str_contains($line, "\n")) {
				$sublines = explode("\n", $line);
				$line = array_shift($sublines);
				$docblock .= " * $line\n";
				$docblock .= $this->writeLines($sublines);
			} else {
				$docblock .= " * $line\n";
			}
		}

		return $docblock;
	}

	/**
	 * Magic toString() method
	 * 
	 * @return string
	 */
	public function __toString(): string {
		return $this->toString();
	}
}
