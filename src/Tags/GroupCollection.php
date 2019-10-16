<?php
declare(strict_types=1);

namespace xiian\PHPDocFormatters\Tags;

class GroupCollection implements \IteratorAggregate
{
    /** @var Group[] */
    protected $groups = [];

    /**
     * @param Group[] $groups
     */
    public function __construct(array $groups = [])
    {
        $this->groups = $groups;
    }

    public function getIterator()
    {
        foreach ($this->groups as $group) {
            $label = $group->getLabel();
            if (empty($label)) {
                yield $group->getTags();
            } else {
                yield $label => $group->getTags();
            }
        }
    }

}
