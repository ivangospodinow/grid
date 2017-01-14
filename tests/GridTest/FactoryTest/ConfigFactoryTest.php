<?php
namespace GridTest\FactoryTest;

use Grid\Factory\StaticFactory;
use Grid\Grid;
use Grid\Renderer\CliRenderer;

use PHPUnit\Framework\TestCase;

class ConfigFactoryTest extends TestCase
{
    public function testConfigFactory()
    {
        $config = [
            'grid' => [
                'MyFirstGrid' => [
                    'columns' => [
                        [
                            'class' => \Grid\Column\Column::class,
                            'options' => [
                                'name' => 'productId',
                                'label' => '#',
                                'dbFields' => 'productId',
                                'sortable' => true,
                            ]
                        ],
                    ],
                    'plugins' => [
                        [
                            'class' => \Grid\Source\ArraySource::class,
                            'options' => [
                                'driver' => [],
                                'start' => 0,
                                'end' => 10,
                                'order' => ['name' => 'ASC']
                            ]
                        ],
                    ],
                    'profiles' => [
                        'admin' => [
                            'columns' => [
                                0 => 'productId',
                            ],
                            'plugins' => [

                            ]
                        ]
                    ]
                ],
                'plugins' => [
                    \Grid\Renderer\HtmlRenderer::class,
                //    \Grid\Renderer\DataTablesRenderer::class,
                    \Grid\Plugin\PaginationPlugin::class,
                    [
                        'class' => \Grid\Plugin\HeaderPlugin::class,
                        'options' => [
                            'position' => \Grid\Plugin\HeaderPlugin::POSITION_BOTH,
                        ]
                    ],
                    [
                        'class' => \Grid\Plugin\ColumnFilterablePlugin::class,
                        'options' => [
                            'markMatches' => true,
                        ]
                    ],
                ],
            ],
        ];

        $grid = \Grid\Factory\ConfigFactory::factory('MyFirstGrid', 'admin', $config);
        $html = $grid->render();

    }

    public function testConfigMinimalFactory()
    {
        $config = [
            'grid' => [
                'MyFirstGrid' => [
                    'columns' => [
                        [
                            'class' => \Grid\Column\Column::class,
                            'options' => [
                                'name' => 'productId',
                                'label' => '#',
                                'dbFields' => 'productId',
                                'sortable' => true,
                            ]
                        ],
                    ],
                    'profiles' => [
                        'admin' => [
                            'columns' => [
                                0 => 'productId',
                            ],
                            'plugins' => [

                            ]
                        ]
                    ]
                ],
            ],
        ];

        $grid = \Grid\Factory\ConfigFactory::factory('MyFirstGrid', 'admin', $config);
        $html = $grid->render();

    }
}
