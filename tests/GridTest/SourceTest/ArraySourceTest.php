<?php
namespace GridTest\HydratorTest;

use Grid\Grid;
use Grid\Source\ArraySource;

use PHPUnit\Framework\TestCase;

use \Exception;

class ArraySourceTest extends TestCase
{
    protected $data = [
        [1, 2],
        [3, 4],
        [5, 6]
    ];
    
    public function testConstructorr()
    {
        try {
            $source = new ArraySource([]);
            $this->assertTrue(false);
        } catch (Exception $e) {
            $this->assertTrue(true);
        }

        try {
            $source = new ArraySource(['driver' => '']);
            $this->assertTrue(false);
        } catch (Exception $e) {
            $this->assertTrue(true);
        }
        
        try {
            $source = new ArraySource(['driver' => $this]);
            $this->assertTrue(false);
        } catch (Exception $e) {
            $this->assertTrue(true);
        }

        try {
            $source = new ArraySource(['driver' => []]);
            $this->assertTrue(true);
        } catch (Exception $e) {
            $this->assertTrue(false);
        }
    }

    public function testMethods()
    {
        $source = $this->getSource();
        $this->assertTrue(count($source->getRows()) === 3);
        $source->setRows($this->data);
        $this->assertTrue(count($source->getRows()) === 3);
    }

    /**
     *
     * @return ArraySource
     */
    public function getSource($config = [])
    {
        return new ArraySource(['driver' => $this->data] + $config);
    }
}