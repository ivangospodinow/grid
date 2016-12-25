<?php
namespace GridTest\ColumnTest;

use Grid\Grid;
use Grid\Column\Column;

use PHPUnit\Framework\TestCase;

class ColumnTest extends TestCase
{
    public function testGridEmptyConstruct()
    {
        try {
            $instance = new Column([]);
            $this->assertTrue(false);
        } catch (\Exception $e) {
            $this->assertTrue(true);
        }
    }

    public function testLabel()
    {
        $instance = $this->getInstance();
        $this->assertTrue($instance->getLabel() === '');

        $instance->setLabel('LABEL');
        $this->assertTrue($instance->getLabel() === 'LABEL');

        $this->assertTrue($instance->getPreLabel() === '');
        $this->assertTrue($instance->getPostLabel() === '');

        $instance->setPreLabel('1');
        $instance->setPostLabel('2');
        $this->assertTrue($instance->getLabel() === 'LABEL');
        $this->assertTrue($instance->getPreLabel() === '1');
        $this->assertTrue($instance->getPostLabel() === '2');
    }

    public function testExtract()
    {
        $instance = $this->getInstance();
        $this->assertTrue($instance->getExtract() === 'test');

        $instance = $this->getInstance(['extract' => 'getName']);
        $this->assertTrue($instance->getExtract() === 'getName');

        $instance = $this->getInstance(['extract' => ['getName']]);
        $this->assertTrue($instance->getExtract()[0] === 'getName');
    }

    public function testDbFields()
    {
        $instance = $this->getInstance();
        $this->assertTrue(is_array($instance->getDbFields()));
        $this->assertFalse($instance->hasDbFields());

        $instance = $this->getInstance(['dbFields' => 'test']);
        $this->assertTrue(is_array($instance->getDbFields()));
        $this->assertTrue($instance->getDbFields()[0] === 'test');
        $this->assertTrue($instance->hasDbFields());
    }

    public function testDataType()
    {
        $instance = $this->getInstance();
        $this->assertFalse($instance->hasDataType());

        $instance = $this->getInstance(['dataType' => \Grid\DataType\Integer::class]);
        $this->assertTrue($instance->hasDataType());
        $this->assertTrue($instance->getDataType() === \Grid\DataType\Integer::class);

        $instance = $this->getInstance(['dataType' => 'no_such_class']);
        $this->assertFalse($instance->hasDataType());
        $this->assertTrue($instance->getDataType() === 'no_such_class');
    }

    public function testFormat()
    {
        $instance = $this->getInstance();
        $this->assertNull($instance->getFormat());

        $instance = $this->getInstance(['format' => 123]);
        $this->assertTrue($instance->getFormat() === 123);

        $instance->setFormat($this);
        $this->assertTrue($instance->getFormat() === $this);
    }

    public function testSortable()
    {
        $instance = $this->getInstance();
        $this->assertFalse($instance->isSortable());

        $instance = $this->getInstance(['sortable' => true]);
        $this->assertFalse($instance->isSortable());

        $instance = $this->getInstance(['sortable' => true, 'dbFields' => ['test']]);
        $this->assertTrue($instance->isSortable());

        $instance = $this->getInstance(['sortable' => true, 'dbFields' => $this]);
        $this->assertFalse($instance->isSortable());
    }

    public function testSearchables()
    {
        $instance = $this->getInstance();
        $this->assertFalse($instance->isSearchable());

        $instance = $this->getInstance(['searchable' => true]);
        $this->assertFalse($instance->isSearchable());

        $instance = $this->getInstance(['searchable' => true, 'dbFields' => ['test']]);
        $this->assertTrue($instance->isSearchable());

        $instance = $this->getInstance(['searchable' => true, 'dbFields' => $this]);
        $this->assertFalse($instance->isSearchable());
    }

    public function testExtractor()
    {
        $instance = $this->getInstance();
        try {
            $extractor = $instance->getExtractor();
            $this->assertTrue(false);
        } catch (\Exception $ex) {
            $this->assertTrue(true);
        }

        try {
            $instance = $this->getInstance();
            $instance->getExtractor('test');
        } catch (\Exception $ex) {
            $this->assertTrue(true);
        }

        $instance = $this->getInstance();
        $this->assertTrue($instance->getExtractor([]) instanceof \Grid\Util\Extractor\AbstractExtractor);
        
        $instance = $this->getInstance();
        $this->assertTrue($instance->getExtractor(new \stdClass) instanceof \Grid\Util\Extractor\AbstractExtractor);
    }

    public function getInstance($config = [])
    {
        $instance = new Column(['name' => 'test'] + $config);
        $instance->setGrid(new Grid);
        return $instance;
    }
}