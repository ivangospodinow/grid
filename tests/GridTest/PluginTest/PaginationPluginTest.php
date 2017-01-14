<?php
namespace GridTest\HydratorTest;

use Grid\Grid;
use Grid\Plugin\PaginationPlugin;
use Grid\Util\Links;
use Grid\Source\ArraySource;

use PHPUnit\Framework\TestCase;

class PaginationPluginTest extends TestCase
{
    public function testPaginationPlugin()
    {
        $grid = new Grid;
        $grid[] = new Links(
            [
                'params' => [
                    'grid' => [
                        'grid-id' => [
                            'page' => 2
                        ]
                    ]
                ],
            ]
        );
        
        $pagination = new PaginationPlugin(['itemsPerPage' => 13]);
        $pagination->setGrid($grid);

        $source = new ArraySource(['driver' => []]);

        $pagination->filterSource($source);

        $this->assertTrue($source->getLimit() === 13);
        $this->assertTrue($source->getLimit() === $source->getEnd() - $source->getStart());
    }
}
