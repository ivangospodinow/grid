<?php
require_once 'autoload.php';

//$config = [
//    'renderer' => \Grid\Renderer\CliRenderer::class,
//    'source' => [
//        [
//            'type' => 'array',
//            'data' =>     [
//                [
//                    'id' => 1,
//                    'name' => 'Ivan Gospodinow',
//                ],
//                [
//                    'id' => 2,
//                    'name' => 'Ivan Gospodinow The Second',
//                ]
//            ]
//        ]
//    ],
//    'columns' => [
//        ['name' => 'id', 'label' => '#'],
//        ['name' => 'name', 'label' => 'Name']
//    ],
//    'profile' => [
//        'columns' => ['id', 'name']
//    ],
//];
//
//$grid = Grid\Grid::factory($config);
//$grid->render();
//die;
//var_dump($grid);die;



$grid = new Grid\Grid;
$grid[] = new Grid\Column\Column('id', '#');
$grid[] = new Grid\Column\Column('name', 'Name');
//$grid[] = new Grid\Source\ArraySource(
//    [
//        [
//            'id' => 1,
//            'name' => 'Ivan Gospodinow',
//        ],
//        [
//            'id' => 2,
//            'name' => 'Ivan Gospodinow The Second',
//        ]
//    ]
//);

//$pdo = new PDO('mysql:host=localhost;dbname=test;charset=UTF8', 'root', '');
//
//$grid[] = $source = new \Grid\Source\MysqlPdoSource(
//    [
//        'driver' => $pdo,
//        'table'  => 'users',
//        'start'  => 0,
//        'end'    => 10,
//    ]
//);

$mysqli = new mysqli('localhost', 'root', '', 'test');
$grid[] = $source = new \Grid\Source\MysqliSource(
    [
        'driver' => $mysqli,
        'table'  => 'users',
        'start'  => 0,
        'end'    => 10,
    ]
);


//var_dump($source->getR/ows());die;

//$grid[] = new Grid\Renderer\CliRenderer();
$grid[] = new Grid\Renderer\HtmlRenderer;
//$grid[] = new Grid\Grid;
//var_dump($grid);
//var_dump($grid->getData());
//var_dump($grid->getColumns());
$grid[] = new Grid\Plugin\ProfilePlugin(
    [
        'columns' => ['id', 'name']
    ]
);
$grid[] = new Grid\Plugin\PaginationPlugin;
echo $grid->render();