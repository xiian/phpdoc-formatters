<?php
declare(strict_types=1);

namespace xiian\PHPDocFormatters\Tags\Organization;

use xiian\PHPDocFormatters\Tags\GroupCollection;

interface Strategy
{
    public function organizeTags($docblock): GroupCollection;
}
