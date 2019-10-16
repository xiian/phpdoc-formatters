<?php
declare(strict_types=1);

namespace xiian\PHPDocFormatters\test\Tags;

use phpDocumentor\Reflection\DocBlock\Tags\Generic;
use PHPUnit\Framework\TestCase;
use xiian\PHPDocFormatters\Tags\Group;
use xiian\PHPDocFormatters\Tags\GroupCollection;

/**
 * @coversDefaultClass \xiian\PHPDocFormatters\Tags\GroupCollection
 * @uses \xiian\PHPDocFormatters\Tags\GroupCollection::__construct
 */
class GroupCollectionTest extends TestCase
{
    /**
     * @covers ::getIterator
     */
    public function testGetIteratorReturnsIterator()
    {
        $sut = new GroupCollection([]);

        $out = $sut->getIterator();
        $this->assertIsIterable($out);
    }

    /**
     * @covers ::getIterator
     * @covers ::__construct
     * @uses \xiian\PHPDocFormatters\Tags\Group
     */
    public function testGetIteratorReturnsProperFormat()
    {
        $g1Label = 'Group1';
        $g1Tags  = [new Generic('generictag1')];
        $group1  = new Group($g1Tags, $g1Label);

        $g2Label = 'Group2';
        $g2Tags  = [new Generic('generictag2')];
        $group2  = new Group($g2Tags, $g2Label);

        $groups = [
            $group1,
            $group2,
        ];
        $expect = [
            $g1Label => $g1Tags,
            $g2Label => $g2Tags,
        ];

        $sut = new GroupCollection($groups);

        $array = iterator_to_array($sut->getIterator());
        $this->assertSame($expect, $array);
    }

    /**
     * @covers ::getIterator
     * @covers ::__construct
     * @uses \xiian\PHPDocFormatters\Tags\Group
     */
    public function testGetIteratorReturnsProperFormatWithNoLabels()
    {
        $g1Tags  = [new Generic('generictag1')];
        $group1  = new Group($g1Tags);

        $g2Tags  = [new Generic('generictag2')];
        $group2  = new Group($g2Tags);

        $groups = [
            $group1,
            $group2,
        ];
        $expect = [
            $g1Tags,
            $g2Tags,
        ];

        $sut = new GroupCollection($groups);

        $array = iterator_to_array($sut->getIterator());
        $this->assertSame($expect, $array);
    }
}
