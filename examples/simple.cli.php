<?php
require '../autoload.php';

$config = [];
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
$config[] = \Grid\Renderer\CliRenderer::class;

$grid = \Grid\Factory\StaticFactory::factory($config);


echo '<pre>' . PHP_EOL;
echo $grid->render();

/*SPLIT*/
echo sprintf(
    '<pre><code>%s</code></pre>',
    htmlspecialchars(explode('/*SPLIT*/', file_get_contents(__FILE__))[0])
);

echo '<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/highlight.js/9.9.0/styles/github.min.css">
<script src="//cdnjs.cloudflare.com/ajax/libs/highlight.js/9.9.0/highlight.min.js"></script>
<script>hljs.initHighlightingOnLoad();</script>';