<?php
namespace GridTest\SourceTest;

use Grid\Grid;
use Grid\Source\MysqlPdoSource;

use Zend\Db\Adapter\Adapter;

use PHPUnit\Framework\TestCase;

use \Exception;

class MysqlPdoSourceTest extends TestCase
{
    public function testConstructor()
    {
        require_once __DIR__ . '/../..//mockup/Sql.php';
        try {
            $source = new MysqlPdoSource([]);
            $this->assertTrue(false);
        } catch(Exception $e) {
            $this->assertTrue(true);
        }

        $source = new MysqlPdoSource(['table' => 'test', 'driver' => new \PDO('mysql:host=localhost;dbname=test;charset=UTF8', 'root', '')]);

    }
}
