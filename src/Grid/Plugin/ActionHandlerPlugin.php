<?php

namespace Grid\Plugin;

use Grid\Grid;
use Grid\Util\Traits\GridAwareTrait;
use Grid\Util\Traits\LinkCreatorAwareTrait;
use Grid\Interfaces\GridInterface;
use Grid\Interfaces\ActionHandleInterface;
use Grid\Interfaces\ActionHandlerInterface;
use Grid\Interfaces\RenderPluginInterface;

/**
 * @author Ivan Gospodinow <ivangospodinow@gmail.com>
 */
class ActionHandlerPlugin implements ActionHandlerInterface, RenderPluginInterface, GridInterface
{
    use GridAwareTrait, LinkCreatorAwareTrait;
    
    public function handle(Grid $grid) : Grid
    {
        foreach ($grid[ActionHandleInterface::class] as $action) {
            $action->handle($this->getData($grid));
        }
        return $grid;
    }

    /**
     *
     * @param string $html
     */
    public function preRender(string $html) : string
    {
        $this->handle($this->getGrid());
        return $html;
    }

    /**
     *
     * @param string $html
     */
    public function postRender(string $html) : string
    {
        return $html;
    }

    public function getData(Grid $grid)
    {
        return $this->getLinkCreator()->getPost();
    }
}
