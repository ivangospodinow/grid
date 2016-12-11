<?php
require_once 'autoload.php';

$grid = new Grid\Grid;
$grid[] = new Grid\Column\Column('id', '#');
$grid[] = new Grid\Column\Column('name', 'Name');
$grid[] = new Grid\Source\ArraySource(
    [
        [
            'id' => 1,
            'name' => 'Ivan Gospodinow',
        ],
        [
            'id' => 2,
            'name' => 'Ivan Gospodinow The Second',
        ]
    ]
);
//$grid[] = new Grid\Renderer\CliRenderer();
$grid[] = new Grid\Renderer\HtmlRenderer;
//$grid[] = new Grid\Grid;
//var_dump($grid);
//var_dump($grid->getData());
//var_dump($grid->getColumns());
$grid[] = new Grid\Plugin\ProfilePlugin(
    [
        'columns' => ['name']
    ]
);
$grid[] = new Grid\Plugin\PaginationPlugin;
echo $grid->render();