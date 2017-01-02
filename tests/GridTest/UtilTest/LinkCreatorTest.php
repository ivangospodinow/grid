<?php
namespace GridTest\HydratorTest;

use Grid\Util\Links;
use Grid\Column\Column;
use Grid\Grid;
use Grid\Util\LinkCreator;

use PHPUnit\Framework\TestCase;

class LinkCreatorTest extends TestCase
{
    public function testLinkCreator()
    {
        $link = new Links;
        $grid = new Grid;
        $grid[] = $link;
        
        $creator = new LinkCreator;
        $creator->setGrid($grid);

       $_SERVER['REQUEST_URI'] = '/?test=1';
        $this->assertTrue($creator->getPageBasePath() === '/');
    }
}