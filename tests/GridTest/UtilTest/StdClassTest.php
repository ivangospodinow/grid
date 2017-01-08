<?php
namespace GridTest\UtilTest;

use Grid\Util\StdClass;

use PHPUnit\Framework\TestCase;

use \Exception;

class StdClassTest extends TestCase
{
    public function testHydrator()
    {
        $array = [
            'id' => 1,
            'name' => 'Ivan'
        ];
        $std = new StdClass;
        $std->exchangeArray($array);

        $this->assertTrue($std->getId() === 1);
        $this->assertTrue($std->getName() === 'Ivan');

        try {
            $std->name();
            $this->assertTrue(false);
        } catch (Exception $e) {
            $this->assertTrue(true);
        }

        try {
            $std->getNameTest();
            $this->assertTrue(false);
        } catch (Exception $e) {
            $this->assertTrue(true);
        }

        try {
            $std->setName();
            $this->assertTrue(false);
        } catch (Exception $e) {
            $this->assertTrue(true);
        }

        $std->setName('Ivan');
        $this->assertTrue($std->getName() === 'Ivan');
    }
}