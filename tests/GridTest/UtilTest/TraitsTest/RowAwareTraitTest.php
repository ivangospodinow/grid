<?php
namespace GridTest\UtilTest\TraitsTest;

use Grid\Util\Traits\RowAwareTrait;
use Grid\Factory\StaticFactory;
use Grid\Row\BodyRow;

use PHPUnit\Framework\TestCase;

class RowAwareTraitTest extends TestCase
{
    use RowAwareTrait;

    /**
     * @group test
     */
    public function test()
    {
        $config = [
            \Grid\Renderer\HtmlRenderer::class,
            [
                'class' => \Grid\Column\Column::class,
                'options' => [
                    'name' => 'id',
                    'label' => '#',
                    'dbFields' => 'id',
                    'sortable' => true,
                    'selectable' => true,
                    'searchable' => true,
                ]
            ],
            [
                'class' => \Grid\Column\Column::class,
                'options' => [
                    'name' => 'name',
                    'label' => 'Name',
                    'dbFields' => 'name',
                    'sortable' => true,
                    'selectable' => true,
                ]
            ],
            [
                'class' => \Grid\Source\ArraySource::class,
                'options' => [
                    'driver' => [
                        ['id' => 1, 'name' => 'name 1'],
                        ['id' => 2, 'name' => 'name 2']
                    ],
                ]
            ],
            [
                'class' => \Grid\Plugin\ColumnFilterablePlugin::class,
                'options' => [
                    'markMatches' => true,
                ]
            ],
        ];
        
        $grid = StaticFactory::factory($config);

        $this->assertTrue(count($this->getIndexRows($grid->getData(), BodyRow::class, BodyRow::INDEX)) === 2);
    }
}