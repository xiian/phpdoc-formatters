<?php
declare(strict_types=1);

namespace xiian\PHPDocFormatters;

use phpDocumentor\Reflection\DocBlock;
use xiian\PHPDocFormatters\Tags\Formatter\AlignBetterFormatter;
use xiian\PHPDocFormatters\Tags\Organization\Strategy;

class Serializer extends DocBlock\Serializer
{
    /** @var Strategy */
    protected $organizationStrategy;

    public function __construct($indent = 0, $indentString = ' ', $indentFirstLine = true, $lineLength = null, $tagFormatter = null)
    {
        if ($tagFormatter === null) {
            $tagFormatter = new AlignBetterFormatter();
        }
        parent::__construct($indent, $indentString, $indentFirstLine, $lineLength, $tagFormatter);
    }

    /**
     * Assert that the given docblock has all methods needed
     *
     * Since DocBlock is marked final without an interface, this psuedo-duck-typing is the best we can do :-(
     *
     * @param mixed $docblock
     *
     * @throws \InvalidArgumentException
     */
    public static function assertDocblockIsAcceptable($docblock): void
    {
        $methods        = ['getDescription', 'getSummary'];
        $missingMethods = array_filter($methods, function($method) use ($docblock) {
            return !method_exists($docblock, $method);
        });
        if (count($missingMethods)) {
            throw new \InvalidArgumentException('Docblock provided does not implement the needed method(s): ' . implode(', ', $missingMethods));
        }
    }

    /**
     * @param DocBlock $docblock
     */
    public function getDocComment($docblock): string
    {
        static::assertDocblockIsAcceptable($docblock);
        $indent      = str_repeat($this->indentString, $this->indent);
        $firstIndent = $this->isFirstLineIndented ? $indent : '';
        // 3 === strlen(' * ')
        $wrapLength = $this->lineLength ? $this->lineLength - strlen($indent) - 3 : null;

        $text = $this->removeTrailingSpaces(
            $indent,
            $this->addAsterisksForEachLine(
                $indent,
                $this->getSummaryAndDescriptionTextBlock($docblock, $wrapLength)
            )
        );

        $comment = "{$firstIndent}/**\n";
        if ($text) {
            $comment .= "{$indent} * {$text}\n";
            $comment .= "{$indent} *\n";
        }

        $comment = $this->addTagBlock($docblock, $wrapLength, $indent, $comment);
        $comment .= $indent . ' */';

        return $comment;
    }

    private function removeTrailingSpaces(string $indent, string $text): string
    {
        return str_replace("\n{$indent} * \n", "\n{$indent} *\n", $text);
    }

    private function addAsterisksForEachLine(string $indent, string $text): string
    {
        return str_replace("\n", "\n{$indent} * ", $text);
    }

    /**
     * @param DocBlock $docblock
     * @param int      $wrapLength
     */
    private function getSummaryAndDescriptionTextBlock($docblock, ?int $wrapLength): string
    {
        $description = (string) $docblock->getDescription();

        $text = $docblock->getSummary() . ($description ? "\n\n" . $docblock->getDescription() : '');

        if ($wrapLength !== null) {
            $text = wordwrap($text, $wrapLength);
            return $text;
        }

        return $text;
    }

    /**
     * @param DocBlock $docblock
     * @param int      $wrapLength
     * @param string   $indent
     * @param string   $commentIn
     */
    private function addTagBlock($docblock, ?int $wrapLength, string $indent, string $commentIn): string
    {
        $tagBlock = '';

        foreach ($this->getOrganizationStrategy()->organizeTags($docblock) as $groupLabel => $tags) {
            if ($this->tagFormatter instanceof AlignBetterFormatter) {
                $this->tagFormatter->establishMaxWidths($tags);
            }

            if (!is_int($groupLabel) && !empty($groupLabel)) {
                $commentIn = rtrim($commentIn, " *\n") . "\n";

                $tagBlock .= "{$indent} *\n";
                $tagBlock .= "{$indent} * {$groupLabel}\n";
            }

            foreach ($tags as $tag) {
                $tagText = $this->tagFormatter->format($tag);

                if ($wrapLength !== null) {
                    $tagText = wordwrap($tagText, $wrapLength);
                }

                $tagText = str_replace("\n", "\n{$indent} * ", $tagText);

                $tagBlock .= "{$indent} * {$tagText}\n";
            }
        }

        return $commentIn . $tagBlock;
    }

    public function getOrganizationStrategy(): Strategy
    {
        if (!$this->organizationStrategy) {
            $this->organizationStrategy = new Tags\Organization\BasicStrategy();
        }
        return $this->organizationStrategy;
    }

    public function setOrganizationStrategy(Strategy $organizationStrategy): void
    {
        $this->organizationStrategy = $organizationStrategy;
    }
}
