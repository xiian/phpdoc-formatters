<?php
declare(strict_types=1);

namespace xiian\PHPDocFormatters\Tags\Organization;

use xiian\PHPDocFormatters\Tags\Group;
use xiian\PHPDocFormatters\Tags\GroupCollection;

class BasicStrategy implements Strategy
{
    public function organizeTags($docblock): GroupCollection
    {
        if (method_exists($docblock, 'getTags')) {
            $tags   = $docblock->getTags();
            $groups = [];
            if (count($tags) >= 1) {
                $group  = new Group($tags);
                $groups = [$group];
            }
            return new GroupCollection($groups);
        }

        $type = gettype($docblock);
        if (is_object($docblock)) {
            $type = get_class($docblock);
        }
        throw new \InvalidArgumentException(sprintf('Docblock provided is of an unknown type (%s)', $type));
    }

}
