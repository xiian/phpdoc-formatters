<?php
declare(strict_types=1);

namespace xiian\PHPDocFormatters\test\Tags\Organization;

use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use phpDocumentor\Reflection\DocBlock\Tags\Generic;
use PHPUnit\Framework\TestCase;
use xiian\PHPDocFormatters\Tags\Organization\BasicStrategy;

/**
 * @coversDefaultClass \xiian\PHPDocFormatters\Tags\Organization\BasicStrategy
 * @uses \xiian\PHPDocFormatters\Tags\Group
 * @uses \xiian\PHPDocFormatters\Tags\GroupCollection
 */
class BasicStrategyTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    /** @var BasicStrategy */
    protected $sut;

    protected function setUp(): void
    {
        $this->sut = new BasicStrategy();
    }

    /**
     * @covers ::organizeTags
     */
    public function testOrganizeTagsThrowsExceptionForNonObject()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->sut->organizeTags('');
    }

    /**
     * @covers ::organizeTags
     */
    public function testOrganizeTagsThrowsExceptionForObject()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->sut->organizeTags((object) ['what']);
    }

    /**
     * @covers ::organizeTags
     */
    public function testOrganizeTagsDoesNothing()
    {
        $t1 = new Generic('one');
        $t2 = new Generic('two');
        $t3 = new Generic('three');

        $tags   = [
            $t3,
            $t2,
            $t1,
        ];
        $expect = [
            [
                $t3,
                $t2,
                $t1,
            ],
        ];

        $block = new class($tags) {
            protected $t;
            public function __construct($t)
            {
                $this->t = $t;
            }

            public function getTags()
            {
                return $this->t;
            }
        };

        $out = $this->sut->organizeTags($block);
//        foreach ($out as $k => $v) {
//            error_log($k);
//        }
        $collection = iterator_to_array($out);

        $this->assertSame($expect, $collection);
    }
}
