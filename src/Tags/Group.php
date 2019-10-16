<?php
declare(strict_types=1);

namespace xiian\PHPDocFormatters\Tags;

use phpDocumentor\Reflection\DocBlock\Tag;

class Group
{
    /** @var string */
    protected $label;

    /** @var Tag[] */
    protected $tags = [];

    public function __construct(array $tags, string $label = '')
    {
        $this->label = $label;

        foreach ($tags as $t) {
            if (!($t instanceof Tag)) {
                throw new \InvalidArgumentException(sprintf('%s requires an array of %s objects.', __CLASS__, Tag::class));
            }
        }
        $this->tags = $tags;
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    /**
     * @return Tag[]
     */
    public function getTags(): array
    {
        return $this->tags;
    }

}
