<?php
namespace GridTest;

use Grid\Grid;
use Grid\Interfaces\TranslateInterface;
use Grid\Renderer\HtmlRenderer;

use PHPUnit\Framework\TestCase;

class GridTest extends TestCase implements TranslateInterface
{
    public function testGridEmptyConstruct()
    {
        $instance = new Grid;
    }

    public function testIterator()
    {
        $instance = new Grid(['autoload' => false]);
        $instance[] = $this;
        $this->assertTrue($instance->hasObject(self::class));
        $this->assertTrue($instance->offsetGet(0) instanceof self);
        
        $instance->offsetUnset(0);
        $this->assertFalse($instance->hasObject(self::class));
        $instance->offsetSet('test', $this);
        $this->assertTrue($instance->hasObject(self::class));

        try {
            $instance['test'] = new \DateTime;
            $this->assertTrue(false);
        } catch (\Exception $e) {
            $this->assertTrue(true);
        }
    }

    public function testGridId()
    {
        $id = 'my-id';
        $instance = new Grid(['id' => $id]);
        $this->assertTrue(is_string($instance->getId()));
        $this->assertTrue($instance->getId() === $id);
    }

    public function testAutoload()
    {
        $instance = new Grid(['autoload' => true]);
        $this->assertAttributeEquals(true, 'autoload', $instance);
        $this->assertTrue($instance->hasObject(\Grid\Plugin\AutoloaderPlugin::class));
    }

    public function testColumns()
    {
        $instance = new Grid(['autoload' => true]);
        $instance[] = new \Grid\Column\Column(['name' => 'test', 'dataType' => \Grid\DataType\Str::class]);
        $this->assertTrue(count($instance->getColumns()) === 1);
        $this->assertTrue($instance->hasObject(\Grid\Plugin\DataTypesPlugin::class));

        $this->assertTrue($instance->getColumn('test') instanceof \Grid\Column\AbstractColumn);

        $this->expectException(\Exception::class);
        $this->assertFalse($instance->getColumn('test2') instanceof \Grid\Column\AbstractColumn);
    }

    public function testTranslate()
    {
        $instance = new Grid(['autoload' => true]);
        $this->assertTrue($instance->translate('test') === 'test');

        $instance = new Grid(['autoload' => true]);
        $instance[] = $this;
        $this->assertTrue($instance->translate('test') === '1test');
    }

    public function translate(string $string) : string
    {
        return '1' . $string;
    }

    public function testRender()
    {
        $instance = new Grid(['autoload' => true]);
        $instance[] = new HtmlRenderer;
        $this->assertTrue(is_string($instance->render()));
    }
}