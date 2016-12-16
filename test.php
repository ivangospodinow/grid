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

$array = [];
for ($i = 1; $i <= 1000; $i++) {
    $array[] = [
        'id' => $i,
        'name' => 'Name ' .$i
    ];
}

$grid = new Grid\Grid;
$grid[] = new Grid\Column\Column(
    [
        'name' => 'id',
        'label' => '#',
        'dbFields' => 'id',
        'sortable' => true,
    ]
);
$grid[] = new Grid\Column\Column(
    [
        'name' => 'name',
        'label' => 'Name',
        'dbFields' => 'name',
        'sortable' => true,
    ]
);
//$grid[] = new Grid\Source\ArraySource(
//    [
//        'driver' => $array,
//        'start' => 0,
//        'end' => 10,
//        'order' => ['name' => 'ASC']
//    ]
//);

//Create a simple "default" Doctrine ORM configuration for Annotations
//$isDevMode = true;
//$config = \Doctrine\ORM\Tools\Setup::createAnnotationMetadataConfiguration(array(__DIR__."/src"), $isDevMode);
////..
//$connectionParams = array(
//    'dbname' => 'test',
//    'user' => 'root',
//    'password' => '',
//    'host' => 'localhost',
//    'driver' => 'pdo_mysql',
//);
//$conn = \Doctrine\DBAL\DriverManager::getConnection($connectionParams, $config);
//// obtaining the entity manager
//$entityManager = \Doctrine\ORM\EntityManager::create($conn, $config);
//
//
////var_dump($entityManager);die;
//$grid[] = $source = new \Grid\Source\DoctrineSource(
//    [
//        'driver' => $entityManager,
//        'namespace' => 't',
//        'table'  => 'users',
//        'start'  => 0,
//        'end'    => 10,
//    ]
//);

$pdo = new PDO('mysql:host=localhost;dbname=test;charset=UTF8', 'root', '');

$grid[] = $source = new \Grid\Source\MysqlPdoSource(
    [
        'driver' => $pdo,
        'table'  => 'users',
        'start'  => 0,
        'end'    => 10,
    ]
);

//$mysqli = new mysqli('localhost', 'root', '', 'test');
//$grid[] = $source = new \Grid\Source\MysqliSource(
//    [
//        'driver' => $mysqli,
//        'table'  => 'users',
//        'start'  => 0,
//        'end'    => 10,
//    ]
//);


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
$grid[] = new Grid\Hydrator\Hydrator;
$grid[] = new Grid\Plugin\ColumnSortablePlugin();
$grid[] = new \Grid\Util\Links();

echo $grid->render();