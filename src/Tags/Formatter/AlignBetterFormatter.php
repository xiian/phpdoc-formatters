<?php
declare(strict_types=1);

namespace xiian\PHPDocFormatters\Tags\Formatter;

use phpDocumentor\Reflection\DocBlock\Tag;
use phpDocumentor\Reflection\DocBlock\Tags\{BaseTag, Formatter};

class AlignBetterFormatter implements Formatter
{
    /**
     * Maximum length of name
     *
     * @var int
     */
    protected $maxName;

    /**
     * Maximum lengths of each index
     *
     * @var int[]
     */
    protected $maxes;

    private $columnCount = 3;

    /**
     * AlignBetterFormatter constructor.
     *
     * @param Tag[] $tags
     */
    public function __construct(array $tags = [])
    {
        $this->establishMaxWidths($tags);
    }

    /**
     * @param Tag[] $tags
     */
    public function establishMaxWidths(array $tags): void
    {
        $this->maxName = 0;

        $this->maxes = array_fill(0, $this->columnCount, 0);
        foreach ($tags as $t) {
            $this->maxName = max($this->maxName, strlen($t->getName()));

            $parts = $this->splitTagIntoParts($t);
            for ($i = 0; $i < $this->columnCount; $i++) {
                $this->maxes[$i] = max(strlen($parts[$i]), $this->maxes[$i]);
            }
        }
    }

    /**
     * @param Tag $tag
     *
     * @return string[]
     */
    public function splitTagIntoParts(Tag $tag): array
    {
        $tagString = (string) $tag;

        // Remove description, if it's there, at the end
        $description = '';
        if ($tag instanceof BaseTag && $tag->getDescription()) {
            $description = (string) $tag->getDescription();
            $tagString   = preg_replace('/\s*' . preg_quote($description, '/') . '$/', '', $tagString);
        }

        $parts = explode(' ', $tagString);

        // Handle special cases
        switch ($tag->getName()) {
            case 'method':
                // Static has to be the first part
                if ($parts[0] != 'static') {
                    array_unshift($parts, '');
                }
                // Collapse the signature
                if (count($parts) > $this->columnCount) {
                    array_splice($parts, 2, count($parts), [implode(' ', array_slice($parts, 2))]);
                }
                break;
            case 'author':
                // Collapse author info
                $parts = ['', '', $tagString];
                break;
        }

        // Stick description back on
        $parts[] = $description;

        // Make sure we have enough parts
        while (count($parts) <= $this->columnCount) {
            array_unshift($parts, '');
        }

        return $parts;
    }

    public function format(Tag $tag): string
    {
        $tagName = '@' . $tag->getName();
        $tagName = str_pad($tagName, $this->maxName + 1);

        $parts = $this->splitTagIntoParts($tag);

        for ($i = 0; $i < $this->columnCount; $i++) {
            $parts[$i] = str_pad($parts[$i], $this->maxes[$i]);
        }

        $imploded = implode(' ', $parts);
        if ($this->maxes[0] === 0 || $this->maxes[1] === 0) {
            $imploded = ltrim($imploded);
        }

        return trim($tagName . ' ' . rtrim($imploded));
    }
}
