<?php
namespace GridTest\HydratorTest;

use Grid\Plugin\ProfilePlugin;
use Grid\Grid;
use Grid\Factory\StaticFactory;
use Grid\Plugin\ColumnsOnlyDataPlugin;

use PHPUnit\Framework\TestCase;

class ProfilePluginTest extends TestCase
{
    public function testHydrator()
    {
        $grid = $this->getGrid();
        $plugin = new ProfilePlugin(['columns' => ['name']]);
        $plugin->setGrid($grid);
        $grid[] = $plugin;
        
        $columns = $grid->getColumns();
        $this->assertTrue($columns[key($columns)]->getName() === 'name');

        $data = $grid->getData();
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
            [
                'class' => \Grid\Source\ArraySource::class,
                'options' => [
                    'driver' => [
                        ['id' => 1, 'name' => 'Ivan']
                    ],
                    'order' => ['name' => 'ASC']
                ]
            ]
        ];
        return StaticFactory::factory($config);
    }
}