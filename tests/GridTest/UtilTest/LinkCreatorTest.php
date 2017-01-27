<?php
namespace GridTest\UtilTest;

use Grid\Util\Links;
use Grid\Column\Column;
use Grid\Grid;
use Grid\Util\LinkCreator;

use PHPUnit\Framework\TestCase;

class LinkCreatorTest extends TestCase
{
    public function testLinkCreator()
    {
        $_SERVER['REQUEST_URI'] = '/?test=1';
        $link = new Links;
        $grid = new Grid;
        $grid[] = $link;

        
        $creator = new LinkCreator;
        $creator->setGrid($grid);
        $this->assertTrue($creator->getPageBasePath() === '/');

        unset($_SERVER['REQUEST_URI']);
        $link = new Links;
        $grid = new Grid;
        $grid[] = $link;


        $creator = new LinkCreator;
        $creator->setGrid($grid);
        $this->assertTrue($creator->getPageBasePath() === '');
    }
}