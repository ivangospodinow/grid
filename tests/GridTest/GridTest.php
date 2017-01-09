<?php
namespace GridTest;

use Grid\Grid;
use Grid\Interfaces\TranslateInterface;
use Grid\Renderer\HtmlRenderer;
use Grid\Util\Hydrator;
use Grid\Source\ArraySource;
use Grid\Factory\StaticFactory;

use PHPUnit\Framework\TestCase;

use \Exception;

class GridTest extends TestCase implements TranslateInterface
{
    protected $data = [
        [1, 2],
        [3, 4],
        [5, 6]
    ];
    
    public function testGridEmptyConstruct()
    {
        $instance = new Grid;
        $this->assertTrue(true);
    }

    public function testIterator()
    {
        $instance = new Grid;
        $instance[] = $this;
        $this->assertTrue(isset($instance[self::class]));
        
        $instance->offsetUnset(self::class);
        $this->assertFalse(isset($instance[self::class]));
        $instance->offsetSet('test', $this);
        $this->assertTrue(isset($instance[self::class]));

        try {
            $instance['test'] = new \DateTime;
            $this->assertTrue(false);
        } catch (\Exception $e) {
            $this->assertTrue(true);
        }

        try {
            $instance[] = 1;
            $this->assertTrue(false);
        } catch (\Exception $e) {
            $this->assertTrue(true);
        }
    }

    public function testGridId()
    {
        $id = 'my-id';
        $instance = new Grid(['id' => $id]);
        $this->assertTrue(is_string($instance->getId()));
        $this->assertTrue($instance->getId() === $id);
    }

    public function testColumns()
    {
        $instance = new Grid;
        $instance[] = new \Grid\Column\Column(['name' => 'test', 'dataType' => \Grid\DataType\Str::class]);
        $this->assertTrue(count($instance->getColumns()) === 1);
        $this->assertTrue(isset($instance[\Grid\Plugin\DataTypesPlugin::class]));

        $this->assertTrue($instance->getColumn('test') instanceof \Grid\Column\AbstractColumn);

        $this->expectException(\Exception::class);
        $this->assertFalse($instance->getColumn('test2') instanceof \Grid\Column\AbstractColumn);
    }

    public function testTranslate()
    {
        $instance = new Grid;
        $this->assertTrue($instance->translate('test') === 'test');

        $instance = new Grid;
        $instance[] = $this;
        $this->assertTrue($instance->translate('test') === '1test');
    }

    public function translate(string $string) : string
    {
        return '1' . $string;
    }

    public function testRender()
    {
        $instance = new Grid;
        $instance[] = new HtmlRenderer;
        $instance[] = new Hydrator;
        $instance[] = new ArraySource(['driver' => $this->data]);
        $this->assertTrue(is_string($instance->render()));
        $this->assertTrue(count($this->data) === $instance->getCount());
    }

    public function testFactory()
    {
        $config = [
            \Grid\Renderer\HtmlRenderer::class,
            \Grid\Plugin\PaginationPlugin::class,
            \Grid\Util\Hydrator::class,
            [
                'class' => \Grid\Plugin\HeaderPlugin::class,
                'options' => [
                    'position' => \Grid\Plugin\HeaderPlugin::POSITION_BOTH,
                ]
            ],
            [
                'class' => \Grid\Column\Column::class,
                'options' => [
                    'name' => 'id',
                    'label' => '#',
                    'dbFields' => 'id',
                    'sortable' => true,
                ]
            ],
            [
                'class' => \Grid\Source\ArraySource::class,
                'options' => [
                    'driver' => [],
                ]
            ],
        ];
        
        $grid = StaticFactory::factory($config);
        $this->assertTrue(count($grid->getColumns()) === 1);
        $this->assertTrue(count($grid[\Grid\Source\ArraySource::class]) === 1);
        $this->assertTrue(count($grid[\Grid\Column\Column::class]) === 1);
        $this->assertTrue(count($grid[\Grid\Plugin\HeaderPlugin::class]) === 1);
        $this->assertTrue(count($grid[\Grid\Util\Hydrator::class]) === 1);

        try {
            $grid = StaticFactory::factory(
                [
                    'Some random class'
                ]
            );
            $this->assertTrue(false);
        } catch (Exception $e) {
            $this->assertTrue(true);
        }

        try {
            $grid = StaticFactory::factory(
                [
                    [
                        'class' => \Grid\Source\ArraySource::class,
                    ],
                ]
            );
            $this->assertTrue(false);
        } catch (Exception $e) {
            $this->assertTrue(true);
        }

        $grid = StaticFactory::factory(
            [
                [
                    'callback' => [\GridTest\GridTest::class, 'createPlugin'],
                    'options' => [
                        'name' => 'id',
                        'label' => '#',
                        'dbFields' => 'id',
                        'sortable' => true,
                    ]
                ],
            ]
        );
        $this->assertTrue(count($grid->getColumns()) === 1);

        $grid = StaticFactory::factory(
            [
                [
                    'callback' => [$this, 'createPlugin'],
                    'options' => [
                        'name' => 'id',
                        'label' => '#',
                        'dbFields' => 'id',
                        'sortable' => true,
                    ]
                ],
            ]
        );
        $this->assertTrue(count($grid->getColumns()) === 1);

        try {
            $grid = StaticFactory::factory(
                [
                    [
                        'callback' => ['NO SUCH CLASS', 'createPlugin'],
                        'options' => [
                            'name' => 'id',
                            'label' => '#',
                            'dbFields' => 'id',
                            'sortable' => true,
                        ]
                    ],
                ]
            );
            $this->assertTrue(false);
        } catch (Exception $e) {
            $this->assertTrue(true);
        }

        try {
            $grid = StaticFactory::factory(
                [
                    [
                        'callback' => [],
                    ],
                ]
            );
            $this->assertTrue(false);
        } catch (Exception $e) {
            $this->assertTrue(true);
        }
        
        try {
            $grid = StaticFactory::factory(
                [
                    null,
                ]
            );
            $this->assertTrue(false);
        } catch (Exception $e) {
            $this->assertTrue(true);
        }

//        $grid = StaticFactory::factory(
//            [
//                $instance = new Grid,
//            ]
//        );
//        $this->assertTrue($grid === $instance);
    }

    public function createPlugin($config)
    {
        return new \Grid\Column\Column($config);
    }
}