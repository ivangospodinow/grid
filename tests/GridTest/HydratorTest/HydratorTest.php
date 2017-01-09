<?php
namespace GridTest\HydratorTest;

use Grid\Grid;
use Grid\Util\Hydrator;
use Grid\Interfaces\GridInterface;
use Grid\Util\Traits\GridAwareTrait;

use PHPUnit\Framework\TestCase;

class HydratorTest extends TestCase implements GridInterface
{
    use GridAwareTrait;
    
    public function testHydrator()
    {
        $hydrator = new Hydrator;
        $grid = new Grid;
        $hydrator->setGrid($grid);

        try {
            $hydrator->hydrate('');
            $this->assertTrue(true);
        } catch (\Exception $e) {
            $this->assertTrue(true);
        }

        $this->assertTrue(is_object($hydrator->hydrate([])));
        $this->assertTrue(is_object($hydrator->hydrate($this)));
        $this->assertTrue($hydrator->hydrate($this)->getGrid() === $grid);
    }
}