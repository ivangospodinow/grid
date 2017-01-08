<?php
namespace GridTest\UtilTest\TraitsTest;

use Grid\Util\Traits\Attributes;

use PHPUnit\Framework\TestCase;

class AttributesTest extends TestCase
{
    use Attributes;
    
    public function test()
    {
        $this->assertTrue(is_array($this->getAttributes()));
        $attributes = ['name' => 'test', 'class' => ''];
        $this->setAttributes($attributes);
        $this->assertTrue(count($this->getAttributes()) === 2);
        $this->assertTrue($this->getAttributes()['name'] === 'test');
        $this->assertTrue($this->getAttribute('name') === 'test');
        $this->assertTrue($this->getAttribute('not exists') === '');

        $this->addAttribute('class', 'button');
        $this->assertTrue($this->getAttribute('class') === 'button');
        $this->assertTrue(strpos($this->getAttributesString(), 'name="') !== false);
        $this->assertTrue(strpos($this->getAttributesString(), 'class="') !== false);

        $this->assertTrue(strpos($this->createAttributesString(['name' => '123']), 'name="123"') !== false);

        $this->addAttribute('attr', 'button');
        $this->assertTrue(strpos($this->getAttributesString(), 'attr="') !== false);
    }
}