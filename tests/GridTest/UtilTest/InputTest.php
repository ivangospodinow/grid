<?php
namespace GridTest\HydratorTest;

use Grid\Util\Input;

use PHPUnit\Framework\TestCase;

class InputTest extends TestCase
{
    public function testHydrator()
    {
        try {
            $input = new Input([]);
            $this->assertTrue(false);
        } catch (\Exception $e) {
            $this->assertTrue(true);
        }

        try {
            $input = new Input(['name' => 'test', 'type' => 'not existing type']);
            $this->assertTrue(false);
        } catch (\Exception $e) {
            $this->assertTrue(true);
        }

        $input = new Input(['name' => 'test', 'value' => '1', 'type' => Input::TYPE_TEXT]);
        $this->assertTrue($input->getName() === 'test');
        $this->assertTrue($input->getValue() === '1');
        $input->setValue('2');
        $this->assertTrue($input->getValue() === '2');
        $this->assertTrue(strpos($input->render(), '<input') !== false);
        $this->assertTrue(strpos($input->render(), '/>') !== false);
        $this->assertTrue(strpos($input->render(), 'name="test"') !== false);
        $this->assertTrue(strpos(Input::createHiddenFromParams(['test' => '123'])['test'], 'name="test"') !== false);
        $this->assertTrue(strpos(Input::createHiddenFromParams(['test' => '123'])['test'], 'value="123"') !== false);
        $this->assertTrue(is_array(Input::createHiddenFromParams([])));

        $input = new Input(['name' => 'test']);
        $this->assertTrue($input->getAttribute('type') === Input::TYPE_TEXT);
    }

    public function testSelect()
    {
        $input = new Input(
            [
                'name' => 'test',
                'type' => Input::TYPE_SELECT,
            ]
        );
        $this->assertTrue(is_string($input->render()));
        $input->setValueOptions(['test' => 'VALUE 123']);
        $this->assertTrue(strpos($input->render(), 'VALUE 123') !== false);
    }
}