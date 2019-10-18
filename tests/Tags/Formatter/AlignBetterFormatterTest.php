<?php
declare(strict_types=1);

namespace xiian\PHPDocFormatters\test\Tags\Formatter;

use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Mockery\Mock;
use phpDocumentor\Reflection\DocBlock\Description;
use phpDocumentor\Reflection\DocBlock\Tag;
use phpDocumentor\Reflection\DocBlock\Tags\{Author, Covers, Deprecated, Example, Generic, Link, Method, Param, Property, PropertyRead, PropertyWrite, Reference\Url, Return_, See, Since, Source, Throws, Uses, Var_, Version};
use phpDocumentor\Reflection\Fqsen;
use phpDocumentor\Reflection\Types\Array_;
use phpDocumentor\Reflection\Types\Object_;
use PHPUnit\Framework\TestCase;
use xiian\PHPDocFormatters\Tags\Formatter\AlignBetterFormatter;

/**
 * @coversDefaultClass \xiian\PHPDocFormatters\Tags\Formatter\AlignBetterFormatter
 *
 * @uses \xiian\PHPDocFormatters\Tags\Formatter\AlignBetterFormatter::splitTagIntoParts()
 */
class AlignBetterFormatterTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    private $description;

    private $type;

    private $argumentsSingle;

    private $argumentsMultiple;

    /**
     * @param $tagName
     * @param $baseString
     *
     * @return Mock|Tag
     */
    public function createMockTag($tagName, $baseString)
    {
        /** @var Mock&Tag $tag */
        $tag = \Mockery::mock(Tag::class);
        $tag->shouldReceive('getName')->andReturn($tagName);
        $tag->shouldReceive('__toString')->andReturn($baseString);
        return $tag;
    }

    public function setUpForProviders(): void
    {
        $this->description       = new Description('Custom Description');
        $this->type              = new Array_();
        $this->argumentsSingle   = [['name' => 'paramName', 'type' => $this->type]];
        $this->argumentsMultiple = [['name' => 'paramName', 'type' => $this->type], ['name' => 'paramName2', 'type' => $this->type]];
    }

    public function multipleTagProvider()
    {
        $this->setUpForProviders();

        $map = [
            '@param                       $paramName                                      Custom Description' => new Param('paramName', null, false, $this->description),
            '@param                       $paramName'                                                         => new Param('paramName'),
            '@param                       ...$paramName                                   Custom Description' => new Param('paramName', null, true, $this->description),
            '@param                       ...$paramName'                                                      => new Param('paramName', null, true),
            '@param                 array $paramName                                      Custom Description' => new Param('paramName', $this->type, false, $this->description),
            '@param                 array $paramName'                                                         => new Param('paramName', $this->type),
            '@param                 array ...$paramName                                   Custom Description' => new Param('paramName', $this->type, true, $this->description),
            '@param                 array ...$paramName'                                                      => new Param('paramName', $this->type, true),

            '@property                    $propertyName                                   Custom Description' => new Property('propertyName', null, $this->description),
            '@property                    $propertyName'                                                      => new Property('propertyName'),
            '@property              array $propertyName                                   Custom Description' => new Property('propertyName', $this->type, $this->description),
            '@property              array $propertyName'                                                      => new Property('propertyName', $this->type),

            '@property-read               $propertyName                                   Custom Description' => new PropertyRead('propertyName', null, $this->description),
            '@property-read               $propertyName'                                                      => new PropertyRead('propertyName'),
            '@property-read         array $propertyName                                   Custom Description' => new PropertyRead('propertyName', $this->type, $this->description),
            '@property-read         array $propertyName'                                                      => new PropertyRead('propertyName', $this->type),

            '@property-write              $propertyName                                   Custom Description' => new PropertyWrite('propertyName', null, $this->description),
            '@property-write              $propertyName'                                                      => new PropertyWrite('propertyName'),
            '@property-write        array $propertyName                                   Custom Description' => new PropertyWrite('propertyName', $this->type, $this->description),
            '@property-write        array $propertyName'                                                      => new PropertyWrite('propertyName', $this->type),

            '@method                array methodName()                                    Custom Description' => new Method('methodName', [], $this->type, false, $this->description),
            '@method                array methodName()'                                                       => new Method('methodName', [], $this->type),
            '@method                array methodName(array $paramName)                    Custom Description' => new Method('methodName', $this->argumentsSingle, $this->type, false, $this->description),
            '@method                array methodName(array $paramName)'                                       => new Method('methodName', $this->argumentsSingle, $this->type),
            '@method                void  methodName()                                    Custom Description' => new Method('methodName', [], null, false, $this->description),
            '@method                void  methodName()'                                                       => new Method('methodName'),
            '@method                void  methodName(array $paramName)                    Custom Description' => new Method('methodName', $this->argumentsSingle, null, false, $this->description),
            '@method                void  methodName(array $paramName)'                                       => new Method('methodName', $this->argumentsSingle),
            '@method                void  methodName(array $paramName, array $paramName2)'                    => new Method('methodName', $this->argumentsMultiple),
            '@method         static array methodName()                                    Custom Description' => new Method('methodName', [], $this->type, true, $this->description),
            '@method         static array methodName()'                                                       => new Method('methodName', [], $this->type, true),
            '@method         static array methodName(array $paramName)'                                       => new Method('methodName', $this->argumentsSingle, $this->type, true),
            '@method         static void  methodName()                                    Custom Description' => new Method('methodName', [], null, true, $this->description),
            '@method         static void  methodName()'                                                       => new Method('methodName', [], null, true),
            '@method         static void  methodName(array $paramName)                    Custom Description' => new Method('methodName', $this->argumentsSingle, null, true, $this->description),
            '@method         static void  methodName(array $paramName)'                                       => new Method('methodName', $this->argumentsSingle, null, true),

            '@author                      My Name <my.name@example.com>'                                       => new Author('My Name', 'my.name@example.com'),
            '@covers                      \Fqsen                                          Custom Description'  => new Covers(new Fqsen('\\Fqsen'), $this->description),
            '@deprecated                  1.2.3                                           Custom Description'  => new Deprecated('1.2.3', $this->description),
            '@example                     example1.php                                    Example Description' => new Example('example1.php', false, 37, 42, 'Example Description'),
            '@generic                                                                     Custom Description'  => new Generic('generic', $this->description),
            '@link                        linkystring                                     Custom Description'  => new Link('linkystring', $this->description),
            '@return                      array                                           Custom Description'  => new Return_($this->type, $this->description),
            '@see                         example1.php                                    Custom Description'  => new See(new Url('example1.php'), $this->description),
            '@since                       1.2.3                                           Custom Description'  => new Since('1.2.3', $this->description),
            '@source                3     13                                              Custom Description'  => new Source(3, 13, $this->description),
            '@throws                      \Exception                                      Custom Description'  => new Throws(new Object_(new Fqsen('\Exception')), $this->description),
            '@uses                        \Fqsen                                          Custom Description'  => new Uses(new Fqsen('\\Fqsen'), $this->description),
            '@var                   array $varName                                        Custom Description'  => new Var_('varName', $this->type, $this->description),
            '@version                     1.2.3                                           Custom Description'  => new Version('1.2.3', $this->description),
        ];

        // Get all the tags
        $tags = array_values($map);

        // Format into expected format
        $r = [];
        foreach ($map as $expect => $tag) {
            $r[get_class($tag) . '-' . spl_object_id($tag)] = [$expect, $tags, $tag];
        }
        return $r;
    }

    /**
     * @dataProvider multipleTagProvider
     *
     * @covers ::format
     * @covers ::__construct
     * @covers ::establishMaxWidths
     */
    public function testFormatWithEstablishedMaxWidths($expect, $tags, $tag)
    {
        $formatter = new AlignBetterFormatter($tags);
        $this->assertEquals($expect, $formatter->format($tag));
    }

    public function splitTagIntoPartsProvider()
    {
        $this->setUpForProviders();
        $r = [];

        $r['Deprecated']               = [['', '', '', ''], new Deprecated()];
        $r['Author']                   = [['', '', 'My Name <my.name@example.com>', ''], new Author('My Name', 'my.name@example.com')];
        $r['Method - Static']          = [['static', 'array', 'methodName()', 'Custom Description'], new Method('methodName', [], $this->type, true, $this->description)];
        $r['Method - Not Static']      = [['', 'array', 'methodName()', 'Custom Description'], new Method('methodName', [], $this->type, false, $this->description)];
        $r['Method - With Parameters'] = [['', 'array', 'methodName(array $paramName, array $paramName2)', 'Custom Description'], new Method('methodName', $this->argumentsMultiple, $this->type, false, $this->description)];
        $r['With Slashes']             = [['', '', '', 'Something with / slashes'], new Generic('generic', new Description('Something with / slashes'))];
        return $r;
    }

    /**
     * @covers ::splitTagIntoParts
     * @dataProvider splitTagIntoPartsProvider
     * @uses         \xiian\PHPDocFormatters\Tags\Formatter\AlignBetterFormatter::__construct
     * @uses         \xiian\PHPDocFormatters\Tags\Formatter\AlignBetterFormatter::establishMaxWidths
     */
    public function testSplitTagIntoParts($expect, $tag)
    {
        $formatter = new AlignBetterFormatter();
        $out       = $formatter->splitTagIntoParts($tag);
        $this->assertEquals(json_encode($expect), json_encode($out));
        $this->assertEquals($expect, $out);
    }

    public function formatProvider()
    {
        $tagName    = 'mockery';
        $baseString = 'C1 C2 C3 R1 R2 R3';
        $r          = [];
        $r[]        = [$tagName, $baseString, [0, 0, 0], '@mockery C1 C2 C3 R1 R2 R3'];
        $r[]        = [$tagName, $baseString, [1, 1, 1], '@mockery C1 C2 C3 R1 R2 R3'];
        $r[]        = [$tagName, $baseString, [5, 5, 5], '@mockery C1    C2    C3    R1 R2 R3'];
        $r[]        = [$tagName, $baseString, [4, 5, 6], '@mockery C1   C2    C3     R1 R2 R3'];
        return $r;
    }

    /**
     * @covers ::format
     * @dataProvider formatProvider
     * @uses         \xiian\PHPDocFormatters\Tags\Formatter\AlignBetterFormatter::__construct
     * @uses         \xiian\PHPDocFormatters\Tags\Formatter\AlignBetterFormatter::establishMaxWidths
     */
    public function testFormatCleaner($tagName, $baseString, $maxes, $expect)
    {
        $tag = $this->createMockTag($tagName, $baseString);

        $formatter = new AlignBetterFormatter();

        // Manually get max widths
        $maxesProp = new \ReflectionProperty(AlignBetterFormatter::class, 'maxes');
        $maxesProp->setAccessible(true);
        $maxesProp->setValue($formatter, $maxes);

        $this->assertEquals($expect, $formatter->format($tag));
    }
}
