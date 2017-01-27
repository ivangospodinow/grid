<?php
namespace GridTest;

use Grid\Grid;
use Grid\Row\BodyRow;

use PHPUnit\Framework\TestCase;

class BodyRowTest extends TestCase
{
    public function __toString()
    {
        return '';
    }

    public function testEmptyConstruct()
    {
        try {
            $instance = new BodyRow;
            $this->assertTrue(false);
        } catch (\Exception $e) {
            $this->assertTrue(true);
        }
    }

    public function testArrayContruct()
    {
        $grid = $this->getGrid();
        $instance = new BodyRow([]);
        $this->assertEmpty((array) $instance);
        $this->assertTrue(is_array($instance->getSource()));
    }

    public function testStdContruct()
    {
        $grid = $this->getGrid();
        $instance = new BodyRow(new \stdClass);
        $this->assertEmpty((array) $instance);
        $this->assertTrue($instance->getSource() instanceof \stdClass);
    }

    public function testStringContruct()
    {
        $instance = new BodyRow('');
        $this->assertEmpty((array) $instance);
        $this->assertTrue(is_string($instance->getSource()));
        $this->assertTrue($instance->isString());
        
        $instance = new BodyRow($this);
        $this->assertEmpty((array) $instance);
        $this->assertTrue($instance->getSource() === $this);
        $this->assertTrue($instance->isString());
    }

    public function getGrid()
    {
        return new Grid;
    }
}