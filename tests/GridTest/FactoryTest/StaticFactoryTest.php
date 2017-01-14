<?php
namespace GridTest\FactoryTest;

use Grid\Factory\StaticFactory;
use Grid\Grid;
use Grid\Renderer\CliRenderer;

use PHPUnit\Framework\TestCase;

class StaticFactoryTest extends TestCase
{
    
    /**
     * @group failing
     */
    public function testStaticFactory()
    {
        $config = [];
        $config[] = $gridInstance = new Grid;
        $config[] = [
            'class' => \Grid\Column\Column::class,
            'options' => [
                'name' => 'id',
                'label' => '#'
            ]
        ];
        $config[] = [
            'class' => \Grid\Column\Column::class,
            'options' => [
                'name' => 'name',
                'label' => 'Name'
            ]
        ];
        $config[] = [
            'class' => \Grid\Source\ArraySource::class,
            'options' => [
                'driver' => [
                    ['id' => 1, 'name' => 'Ivan'],
                    ['id' => 2, 'name' => 'Denis'],
                    ['id' => 3, 'name' => 'Violeta'],
                    ['id' => 4, 'name' => 'Todor']
                ],
                'order' => ['name' => 'ASC']
            ]
        ];
        $config[] = new CliRenderer;

        $grid = \Grid\Factory\StaticFactory::factory($config);
        $this->assertTrue(isset($grid[CliRenderer::class]));
        $this->assertTrue($grid === $gridInstance);
    }
}
