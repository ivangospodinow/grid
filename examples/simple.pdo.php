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
$grid[] = $source = new \Grid\Source\MysqlPdoSource(
    [
        'driver' => new PDO('mysql:host=192.168.0.115;dbname=crawl;charset=UTF8', 'root', 'wankata'),
        'table'  => 'domain',
        'start'  => 0,
        'end'    => 10,
    ]
);
$config[] = \Grid\Renderer\HtmlRenderer::class;

$grid = \Grid\Factory\StaticFactory::factory($config);
$grid->setAttribute('style', 'border:1px solid #aeaeae;');

echo $grid->render();

/*SPLIT*/
echo sprintf(
    '<pre><code>%s</code></pre>',
    htmlspecialchars(explode('/*SPLIT*/', file_get_contents(__FILE__))[0])
);

echo '<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/highlight.js/9.9.0/styles/github.min.css">
<script src="//cdnjs.cloudflare.com/ajax/libs/highlight.js/9.9.0/highlight.min.js"></script>
<script>hljs.initHighlightingOnLoad();</script>';