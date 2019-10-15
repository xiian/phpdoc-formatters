<?php
declare(strict_types=1);

namespace xiian\PHPDocFormatters\test;

use Mockery as m;
use Mockery\Mock;
use phpDocumentor\Reflection\DocBlock;
use phpDocumentor\Reflection\DocBlock\Description;
use PHPUnit\Framework\TestCase;
use xiian\PHPDocFormatters\Serializer;
use xiian\PHPDocFormatters\Tags\Organization\BasicStrategy;
use xiian\PHPDocFormatters\Tags\Organization\Strategy;

/**
 * @coversDefaultClass \xiian\PHPDocFormatters\Serializer
 * @uses \xiian\PHPDocFormatters\Tags\Formatter\AlignBetterFormatter
 * @uses \xiian\PHPDocFormatters\Serializer
 * @uses \xiian\PHPDocFormatters\Tags\Group
 * @uses \xiian\PHPDocFormatters\Tags\GroupCollection
 * @uses \xiian\PHPDocFormatters\Tags\Organization\BasicStrategy
 */
class SerializerTest extends TestCase
{
    use m\Adapter\Phpunit\MockeryPHPUnitIntegration;

    /**
     * @covers ::__construct
     * @covers ::getDocComment
     * @covers ::addAsterisksForEachLine
     * @covers ::addTagBlock
     * @covers ::getSummaryAndDescriptionTextBlock
     * @covers ::removeTrailingSpaces
     * @uses \phpDocumentor\Reflection\DocBlock\Description
     * @uses \phpDocumentor\Reflection\DocBlock\Tags\Formatter\PassthroughFormatter
     * @uses \phpDocumentor\Reflection\DocBlock
     * @uses \phpDocumentor\Reflection\DocBlock\Tags\BaseTag
     * @uses \phpDocumentor\Reflection\DocBlock\Tags\Generic
     */
    public function testReconstructsADocCommentFromADocBlock(): void
    {
        $expected = <<<'DOCCOMMENT'
/**
 * This is a summary
 *
 * This is a description
 *
 * @unknown-tag Test description for the unknown tag
 */
DOCCOMMENT;

        $fixture = new Serializer();

        $docBlock = new DocBlock(
            'This is a summary',
            new Description('This is a description'),
            [
                new DocBlock\Tags\Generic('unknown-tag', new Description('Test description for the unknown tag')),
            ]
        );

        $this->assertSame($expected, $fixture->getDocComment($docBlock));
    }

    /**
     * @covers ::__construct
     * @covers ::getDocComment
     * @covers ::addAsterisksForEachLine
     * @covers ::addTagBlock
     * @covers ::getSummaryAndDescriptionTextBlock
     * @covers ::removeTrailingSpaces
     * @uses \phpDocumentor\Reflection\DocBlock\Description
     * @uses \phpDocumentor\Reflection\DocBlock\Tags\Formatter\PassthroughFormatter
     * @uses \phpDocumentor\Reflection\DocBlock
     * @uses \phpDocumentor\Reflection\DocBlock\Tags\BaseTag
     * @uses \phpDocumentor\Reflection\DocBlock\Tags\Generic
     */
    public function testAddPrefixToDocBlock(): void
    {
        $expected = <<<'DOCCOMMENT'
aa/**
aa * This is a summary
aa *
aa * This is a description
aa *
aa * @unknown-tag Test description for the unknown tag
aa */
DOCCOMMENT;

        $fixture = new Serializer(2, 'a');

        $docBlock = new DocBlock(
            'This is a summary',
            new Description('This is a description'),
            [
                new DocBlock\Tags\Generic('unknown-tag', new Description('Test description for the unknown tag')),
            ]
        );

        $this->assertSame($expected, $fixture->getDocComment($docBlock));
    }

    /**
     * @covers ::__construct
     * @covers ::getDocComment
     * @covers ::addAsterisksForEachLine
     * @covers ::addTagBlock
     * @covers ::getSummaryAndDescriptionTextBlock
     * @covers ::removeTrailingSpaces
     * @uses \phpDocumentor\Reflection\DocBlock\Description
     * @uses \phpDocumentor\Reflection\DocBlock\Tags\Formatter\PassthroughFormatter
     * @uses \phpDocumentor\Reflection\DocBlock
     * @uses \phpDocumentor\Reflection\DocBlock\Tags\BaseTag
     * @uses \phpDocumentor\Reflection\DocBlock\Tags\Generic
     */
    public function testAddPrefixToDocBlockExceptFirstLine(): void
    {
        $expected = <<<'DOCCOMMENT'
/**
aa * This is a summary
aa *
aa * This is a description
aa *
aa * @unknown-tag Test description for the unknown tag
aa */
DOCCOMMENT;

        $fixture = new Serializer(2, 'a', false);

        $docBlock = new DocBlock(
            'This is a summary',
            new Description('This is a description'),
            [
                new DocBlock\Tags\Generic('unknown-tag', new Description('Test description for the unknown tag')),
            ]
        );

        $this->assertSame($expected, $fixture->getDocComment($docBlock));
    }

    /**
     * @covers ::__construct
     * @covers ::getDocComment
     * @covers ::addAsterisksForEachLine
     * @covers ::addTagBlock
     * @covers ::getSummaryAndDescriptionTextBlock
     * @covers ::removeTrailingSpaces
     * @uses \phpDocumentor\Reflection\DocBlock\Description
     * @uses \phpDocumentor\Reflection\DocBlock\Tags\Formatter\PassthroughFormatter
     * @uses \phpDocumentor\Reflection\DocBlock
     * @uses \phpDocumentor\Reflection\DocBlock\Tags\BaseTag
     * @uses \phpDocumentor\Reflection\DocBlock\Tags\Generic
     */
    public function testWordwrapsAroundTheGivenAmountOfCharacters(): void
    {
        $expected = <<<'DOCCOMMENT'
/**
 * This is a
 * summary
 *
 * This is a
 * description
 *
 * @unknown-tag
 * Test
 * description
 * for the
 * unknown tag
 */
DOCCOMMENT;

        $fixture = new Serializer(0, '', true, 15);

        $docBlock = new DocBlock(
            'This is a summary',
            new Description('This is a description'),
            [
                new DocBlock\Tags\Generic('unknown-tag', new Description('Test description for the unknown tag')),
            ]
        );

        $docComment = $fixture->getDocComment($docBlock);
        $this->assertSame($expected, $docComment);
    }

    /**
     * @covers ::__construct
     * @covers ::getDocComment
     * @covers ::addAsterisksForEachLine
     * @covers ::addTagBlock
     * @covers ::getSummaryAndDescriptionTextBlock
     * @covers ::removeTrailingSpaces
     */
    public function testNoExtraSpacesAfterTagRemoval()
    {
        $expected = <<<'DOCCOMMENT'
/**
 * @unknown-tag
 */
DOCCOMMENT;

        $expectedAfterRemove = <<<'DOCCOMMENT_AFTER_REMOVE'
/**
 */
DOCCOMMENT_AFTER_REMOVE;

        $fixture    = new Serializer(0, '', true, 15);
        $genericTag = new DocBlock\Tags\Generic('unknown-tag');

        $docBlock = new DocBlock('', null, [$genericTag]);
        $this->assertSame($expected, $fixture->getDocComment($docBlock));

        $docBlock->removeTag($genericTag);
        $this->assertSame($expectedAfterRemove, $fixture->getDocComment($docBlock));
    }

    /**
     * @covers ::getDocComment
     */
    public function testGetDocCommentChecksTypes()
    {
        $this->expectException(\InvalidArgumentException::class);
        $fixture = new Serializer();
        /** @noinspection PhpParamsInspection */
        $fixture->getDocComment('');
    }

    /**
     * @covers ::getOrganizationStrategy
     */
    public function testGetOrganizationStrategyDefaults()
    {
        $fixture = new Serializer();
        $this->assertInstanceOf(BasicStrategy::class, $fixture->getOrganizationStrategy());
    }

    /**
     * @covers ::setOrganizationStrategy
     * @uses \xiian\PHPDocFormatters\Serializer::getOrganizationStrategy
     */
    public function testSetOrganizationStrategy()
    {
        $fixture = new Serializer();

        /** @var Mock&Strategy $dummy */
        $dummy = m::mock(Strategy::class);

        $fixture->setOrganizationStrategy($dummy);

        $strategy = $fixture->getOrganizationStrategy();

        $this->assertSame($dummy, $strategy);
    }

    public function docblockAssertionProvider()
    {
        yield [false, ''];
        yield [
            false,
            new class {
                public function getDescription()
                {
                }
            },
        ];
        yield [
            false,
            new class {
                public function getSummary()
                {
                }
            },
        ];
        yield [
            true,
            new class {
                public function getDescription()
                {
                }

                public function getSummary()
                {
                }
            },
        ];
    }

    /**
     * @covers ::assertDocblockIsAcceptable
     * @dataProvider docblockAssertionProvider
     */
    public function testAssertDocblockIsAcceptable($acceptable, $docblock)
    {
        if (!$acceptable) {
            $this->expectException(\InvalidArgumentException::class);
        }

        Serializer::assertDocblockIsAcceptable($docblock);

        if ($acceptable) {
            $this->assertTrue(true, 'Dummy assertion for positive case');
        }
    }
}
