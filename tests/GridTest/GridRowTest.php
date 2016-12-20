<?php
namespace GridTest;

use Grid\Grid;
use Grid\GridRow;

use PHPUnit\Framework\TestCase;

class GridRowTest extends TestCase
{
    public function __toString()
    {
        return '';
    }

    public function testEmptyConstruct()
    {
        try {
            $instance = new GridRow;
            $this->assertTrue(false);
        } catch (\Exception $e) {
            $this->assertTrue(true);
        }
    }

    public function testArrayContruct()
    {
        $grid = $this->getGrid();
        $instance = new GridRow([], $grid);
        $this->assertEmpty((array) $instance);
        $this->assertTrue(is_array($instance->getSource()));
    }

    public function testStdContruct()
    {
        $grid = $this->getGrid();
        $instance = new GridRow(new \stdClass, $grid);
        $this->assertEmpty((array) $instance);
        $this->assertTrue($instance->getSource() instanceof \stdClass);
    }

    public function testStringContruct()
    {
        $instance = new GridRow('', $this->getGrid());
        $this->assertEmpty((array) $instance);
        $this->assertTrue(is_string($instance->getSource()));
        $this->assertTrue($instance->isString());
        
        $instance = new GridRow($this, $this->getGrid());
        $this->assertEmpty((array) $instance);
        $this->assertTrue($instance->getSource() === $this);
        $this->assertTrue($instance->isString());
    }

    public function testPosition()
    {
        $instance = new GridRow('', $this->getGrid());
        $this->assertTrue($instance->getPosition() === $instance::POSITION_BODY);
        $this->assertTrue($instance->isBody());

        $instance = new GridRow('', $this->getGrid(), $instance::POSITION_HEAD);
        $this->assertTrue($instance->getPosition() === $instance::POSITION_HEAD);
        $this->assertTrue($instance->isHead());

        $instance = new GridRow('', $this->getGrid(), $instance::POSITION_FOOTER);
        $this->assertTrue($instance->getPosition() === $instance::POSITION_FOOTER);
        $this->assertTrue($instance->isFoot());
    }

    public function getGrid()
    {
        return new Grid;
    }
}