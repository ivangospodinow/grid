<?php
namespace GridTest\UtilTest\TraitsTest;

use Grid\Util\Traits\LinkCreatorAwareTrait;
use Grid\Util\Traits\GridAwareTrait;
use Grid\Interfaces\GridInterface;
use Grid\Grid;
use Grid\Util\LinkCreator;

use PHPUnit\Framework\TestCase;

class LinkCreatorAwareTraitTest extends TestCase implements GridInterface
{
    use LinkCreatorAwareTrait, GridAwareTrait;
    
    public function test()
    {
        $this->setGrid(new Grid);
        $this->assertTrue($this->getLinkCreator() instanceof LinkCreator);
        $this->setLinkCreator(new LinkCreator);
        $this->assertTrue($this->getLinkCreator() instanceof LinkCreator);
    }
}
