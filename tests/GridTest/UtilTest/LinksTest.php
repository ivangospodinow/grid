<?php
namespace GridTest\UtilTest;

use Grid\Util\Links;
use Grid\Column\Column;
use Grid\Grid;

use PHPUnit\Framework\TestCase;

class LinksTest extends TestCase
{
    public function testLinkCreator()
    {
        $_GET = ['123'];
        $link = new Links;
        $this->assertTrue($link->getParams() === $_GET);

        $grid = new Grid;
        $column = new Column(
            [
                'name' => 'test'
            ]
        );

        $link->setGrid($grid);
        $str = $link->createFilterLink($column, 'searchable', 'VALUE');
        $this->assertTrue(strpos($str, 'VALUE') !== false);

        $this->assertTrue($link->getFilterValue($column, 'searchable', 'default') === 'default');
        $link->setParams(
            [
                'grid' => [
                    $grid->getId() => [
                        'searchable' => [
                            $column->getName() => 123
                        ]
                    ]
                ]
            ]
        );
        $this->assertTrue($link->getFilterValue($column, 'searchable') === '123');

        $this->assertTrue(strpos($link->createPaginationLink(123), '123') !== false);
        $this->assertTrue(strpos($link->createPaginationLink(0), '0') === false);

        $this->assertTrue($link->getActivePaginationPage() === 0);

        $link->setParams(
            [
                'grid' => [
                    $grid->getId() => [
                        'page' => 1
                    ]
                ]
            ]
        );
        $this->assertTrue($link->getActivePaginationPage() === 1);

        $this->assertTrue(is_string($link->getPaginationPageName()));
        $_SERVER['REQUEST_URI'] = '/';
        $this->assertTrue($link->getPageBasePath() === '/');
        $_SERVER['REQUEST_URI'] = '/?test=1';
        $this->assertTrue($link->getPageBasePath() === '/');
    }
}