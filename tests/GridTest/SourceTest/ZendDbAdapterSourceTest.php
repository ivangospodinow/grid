<?php
namespace GridTest\SourceTest;

use Grid\Grid;
use Grid\Source\ZendDbAdapterSource;

use Zend\Db\Adapter\Adapter;

use PHPUnit\Framework\TestCase;

use \Exception;

class ZendDbAdapterSourceTest extends TestCase
{
    public function testZendDbAdapterSourceTest()
    {
        try {
            $source = new ZendDbAdapterSource([]);
            $this->assertTrue(false);
        } catch (Exception $e) {
            $this->assertTrue(true);
        }

        require_once __DIR__ . '/../..//mockup/Sql.php';

        try {
            $source = new ZendDbAdapterSource([]);
            $this->assertTrue(false);
        } catch (Exception $e) {
            $this->assertTrue(true);
        }

        try {
            $source = new ZendDbAdapterSource(['driver' => new Adapter]);
            $this->assertTrue(false);
        } catch (Exception $e) {
            $this->assertTrue(true);
        }

        $source = new ZendDbAdapterSource(
            [
                'driver' => new Adapter,
                'table' => 'test',
                'limit' => 1,
                'offset' => 1
            ]
        );
        $source->setGrid(new Grid);
        $source->setOrder(['name' => 'asc']);
        
        $rows = [0 => ['id' => 1], 1 => ['id' => 1]];
        $source->setRows($rows);
        $this->assertTrue(count($source->getRows()) === 2);
        $this->assertTrue($source->getRows()[0]['id'] === 1);

        $query = $source->getQuery();
        $this->assertTrue($query instanceof \Zend\Db\Sql\Select);
        $this->assertTrue($query->getLimit() === 1);
        $this->assertTrue($query->getOffset() === 1);

        $this->assertTrue($source->getCount() === 0);
    }
    
    /**
     * @group failing
     */
    public function testRows()
    {
        require_once __DIR__ . '/../..//mockup/Sql.php';
        $source = new ZendDbAdapterSource(
            [
                'driver' => new Adapter,
                'table' => 'test',
                'limit' => 1,
                'offset' => 1
            ]
        );

        $source->setGrid(new Grid);
        $rows = $source->getRows();
        $this->assertTrue(count($rows) === 2);
    }

    public function testCountPk()
    {
        $source = new ZendDbAdapterSource(
            [
                'driver' => new Adapter,
                'table' => 'test',
                'limit' => 1,
                'offset' => 1,
                'pk' => 'id'
            ]
        );
        $source->setGrid(new Grid);
        $this->assertTrue($source->getCount() === 0);
    }
}