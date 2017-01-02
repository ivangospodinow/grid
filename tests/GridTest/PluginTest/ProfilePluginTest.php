<?php
namespace GridTest\HydratorTest;

use Grid\Plugin\ProfilePlugin;
use Grid\Grid;

use PHPUnit\Framework\TestCase;

class ProfilePluginTest extends TestCase
{
    public function testHydrator()
    {
        $grid = $this->getGrid();
        $plugin = new ProfilePlugin(['columns' => ['name']]);
        $plugin->setGrid($grid);

        $columns = $plugin->filterColumns($grid->getColumns());
        $this->assertTrue(count($columns) === 1);
        $this->assertTrue($columns[key($columns)]->getName() === 'name');

        $data = [
            ['id' => 1, 'name' => 'Ivan']
        ];

        $data = $plugin->filterData($data);
        $this->assertTrue(!isset($data[key($data)]['id']));
        $this->assertTrue(isset($data[key($data)]['name']));
    }

    public function getGrid()
    {
        $config = [
            [
                'class' => \Grid\Column\Column::class,
                'options' => [
                    'name' => 'id',
                    'label' => 'Id',
                ]
            ],
            [
                'class' => \Grid\Column\Column::class,
                'options' => [
                    'name' => 'name',
                    'label' => 'Name',
                ]
            ],
        ];
        return Grid::factory($config);
    }
}