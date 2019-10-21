# Grid (php 7) - Microservices Pattern
Easy to use event driven grid (table) and general purpose data manager

## Strong points
- Easy to extend, plug in and add new functionality.
- Fast, use only the code you need for each case.
- More is less. More functionality less code.

## Goals
- Support wide range of existing code base. (work in progress)

## Grid mind set
The easy way to think about Grid.php as Service Locator.
Grid.php will hold and create (where needed) all objects,
that are needed for data filtering and rendering.

## Code Example

See more: *./examples*

By creating instances
```
$grid = new \Grid\Grid;
$grid[] = new \Grid\Column\Column(
    [
        'name' => 'id',
        'label' => '#'
    ]
);
$grid[] = new \Grid\Column\Column(
    [
        'name' => 'name',
        'label' => 'Name'
    ]
);
$grid[] = new \Grid\Source\ArraySource(
    [
        'driver' => [
            ['id' => 1, 'name' => 'Ivan'],
            ['id' => 2, 'name' => 'Denis'],
            ['id' => 3, 'name' => 'Violeta'],
            ['id' => 4, 'name' => 'Todor']
        ],
        'order' => ['name' => 'ASC']
    ]
);
$grid[] = new \Grid\Renderer\CliRenderer;

echo $grid->render();

Result:
+---+---------+
| # | Name    |
+---+---------+
| 2 | Denis   |
| 1 | Ivan    |
| 4 | Todor   |
| 3 | Violeta |
+---+---------+
```

By creating from config
```
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
echo $grid->render();

Result:
+---+---------+
| # | Name    |
+---+---------+
| 2 | Denis   |
| 1 | Ivan    |
| 4 | Todor   |
| 3 | Violeta |
+---+---------+
```

## Motivation

After years of doing the same dump table coding, finally went to put an end to this madness. This repo will fit all your needs. Try me!

## Installation

```
composer require ivangospodinow/grid
```

## Tests

Currently 90% unit tested.
Run in project directory
```
phpunit
```

## Contributors

Ivan Gospodinow

## License

A short snippet describing the license (MIT, Apache, etc.)
