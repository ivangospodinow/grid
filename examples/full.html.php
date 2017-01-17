<?php
require '../autoload.php';
require './data/products.php';
require './data/TestPlugin.php';

$config = [];
$config[] = [
    'class' => \Grid\Column\Column::class,
    'options' => [
        'name' => 'productId',
        'label' => '#',
        'dbFields' => 'productId',
        'sortable' => true,
    ]
];
$config[] = [
    'class' => \Grid\Column\Column::class,
    'options' => [
        'name' => 'userId',
        'label' => 'User',
        'dbFields' => 'userId',
        'sortable' => true,
        'selectable' => true,
    ]
];
$config[] = [
    'class' => \Grid\Column\Column::class,
    'options' => [
        'name' => 'platformId',
        'label' => 'Platform',
        'dbFields' => 'platformId',
        'sortable' => true,
        'selectable' => true,
    ]
];
$config[] = [
    'class' => \Grid\Column\Column::class,
    'options' => [
        'name' => 'platformKey',
        'label' => 'Platform key',
        'dbFields' => 'platformKey',
        'sortable' => true,
        'selectable' =>  true,
    ]
];
$config[] = [
    'class' => \Grid\Column\Column::class,
    'options' => [
        'name' => 'title',
        'label' => 'Title',
        'dbFields' => 'title',
        'sortable' => true,
        'searchable' => true,
    ]
];
$config[] = [
    'class' => \Grid\Column\Column::class,
    'options' => [
        'name' => 'priceCurrency',
        'label' => 'Price',
        'dbFields' => 'priceCurrency',
        'sortable' => true,
    ]
];
$config[] = [
    'class' => \Grid\Column\Column::class,
    'options' => [
        'name' => 'lastPriceCurrency',
        'label' => 'Last price',
        'dbFields' => 'lastPriceCurrency',
        'sortable' => true,
    ]
];
$config[] = [
    'class' => \Grid\Column\Column::class,
    'options' => [
        'name' => 'initialPriceCurrency',
        'label' => 'Init price',
        'dbFields' => 'initialPriceCurrency',
        'sortable' => true,
    ]
];
$config[] = [
    'class' => \Grid\Column\Column::class,
    'options' => [
        'name' => 'quantity',
        'label' => 'Qty',
        'dbFields' => 'quantity',
        'sortable' => true,
    ]
];
$config[] = [
    'class' => \Grid\Column\Column::class,
    'options' => [
        'name' => 'image',
        'label' => 'Image',
        'dbFields' => 'image',
        'dataType' => \Grid\DataType\Image::class,
    ]
];
$config[] = [
    'class' => \Grid\Column\Column::class,
    'options' => [
        'name' => 'lastPriceUpdate',
        'label' => 'Last update',
        'dbFields' => 'lastPriceUpdate',
        'dataType' => \Grid\DataType\Date::class,
        'sortable' => true,
    ]
];
$config[] = [
    'class' => \Grid\Column\Column::class,
    'options' => [
        'name' => 'lastPriceChange',
        'label' => 'Last price change',
        'dbFields' => 'lastPriceChange',
        'dataType' => \Grid\DataType\TimeAgo::class,
    ]
];
$config[] = [
    'class' => \Grid\Source\ArraySource::class,
    'options' => [
        'driver' => $products,
        'order' => ['productId' => 'ASC']
    ]
];
$config[] = \Grid\Util\Links::class;
$config[] = [
    'class' => Grid\Plugin\LinksPlugin::class,
    'options' => [
        'column'        => 'platformKey',
        'links' => [
            [
                'uri'           => 'https://www.amazon.co.uk/dp/:platformKey',
                'uriParameters' => [
                    'platformKey' => 'platformKey',
                ],
                'attributes' => [
                    'class' => 'btn btn-default',
                    'target' => '_blank'
                ],
            ],
        ]
    ],
];
$config[] = [
    'class' => \Grid\Plugin\ColumnFilterablePlugin::class,
    'options' => [
        'markMatches' => true,
    ]
];
$config[] = \Grid\Renderer\HtmlRenderer::class;
$config[] = \Grid\Plugin\PaginationPlugin::class;
$config[] = \Grid\Plugin\ColumnSortablePlugin::class;
$config[] = \TestPlugin::class;


$grid = \Grid\Factory\StaticFactory::factory($config);

echo $grid->render();

/*SPLIT*/
echo sprintf(
    '<pre><code>%s</code></pre>',
    htmlspecialchars(explode('/*SPLIT*/', file_get_contents(__FILE__))[0])
);

echo '<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/highlight.js/9.9.0/styles/github.min.css">
<script src="//cdnjs.cloudflare.com/ajax/libs/highlight.js/9.9.0/highlight.min.js"></script>
<script>hljs.initHighlightingOnLoad();</script>';