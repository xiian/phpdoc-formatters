<?php
declare(strict_types=1);

namespace xiian\PHPDocFormatters\test\Tags;

use xiian\PHPDocFormatters\Tags\Group;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \xiian\PHPDocFormatters\Tags\Group
 * @uses \xiian\PHPDocFormatters\Tags\Group::__construct
 */
class GroupTest extends TestCase
{

    /**
     * @covers ::getLabel
     */
    public function testGetLabel()
    {
        $label = __METHOD__;
        $sut = new Group([], $label);
        $this->assertEquals($label, $sut->getLabel());
    }

    /**
     * @covers ::getTags
     * @covers ::__construct
     */
    public function testGetTags()
    {
        $tags = [];
        $sut = new Group($tags);
        $this->assertSame($tags, $sut->getTags());
    }

    /**
     * @covers ::__construct
     */
    public function testConstructorChecksTags()
    {
        $this->expectException(\InvalidArgumentException::class);

        new Group(['']);
    }
}
