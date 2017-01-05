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
    'class' => \Grid\Column\Column::class,
    'options' => [
        'name' => 'age',
        'label' => 'Age'
    ]
];
$config[] = [
    'class' => \Grid\Column\Column::class,
    'options' => [
        'name' => 'password',
        'label' => 'Password'
    ]
];
$config[] = [
    'class' => \Grid\Source\ArraySource::class,
    'options' => [
        'driver' => [
            ['id' => 1, 'name' => 'Ivan', 'age' => 20, 'password' => '323232'],
            ['id' => 2, 'name' => 'Denis', 'age' => 18, 'password' => '123456'],
            ['id' => 3, 'name' => 'Violeta', 'age' => 40, 'password' => 'SAFv3#v'],
            ['id' => 4, 'name' => 'Todor', 'age' => 40, 'password' => 'ff!2vdz']
        ],
        'order' => ['name' => 'ASC']
    ]
];
$config[] = [
    'class' => \Grid\Plugin\ProfilePlugin::class,
    'options' => [
        'columns' => [
            'id',
            'name',
            'age'
        ]
    ]
];
$config[] = \Grid\Renderer\CliRenderer::class;

$grid = \Grid\Factory\StaticFactory::factory($config);


echo '<pre>' . PHP_EOL;
echo $grid->render();

/*SPLIT*/
echo sprintf(
    '<pre><code class="php">%s</code></pre>',
    htmlspecialchars(explode('/*SPLIT*/', file_get_contents(__FILE__))[0])
);

echo '<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/highlight.js/9.9.0/styles/github.min.css">
<script src="//cdnjs.cloudflare.com/ajax/libs/highlight.js/9.9.0/highlight.min.js"></script>
<script>hljs.initHighlightingOnLoad();</script>';