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
    
    public function handleAction(Grid $grid) : Grid
    {
        foreach ($grid[ActionHandleInterface::class] as $action) {
            $action->handleAction($this->getData($grid));
        }
        return $grid;
    }

    /**
     *
     * @param string $html
     */
    public function preRender(string $html) : string
    {
        $this->handleAction($this->getGrid());
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
        $data = $this->getLinkCreator()->getPost() + $this->getLinkCreator()->getParams();
        $params = [];
        if (isset($data['grid'][$this->getGrid()->getId()]['action'])) {
            foreach ($data['grid'][$this->getGrid()->getId()]['action'] as $hash => $hashParams) {
                $action = json_decode(base64_decode($hash), true);
                $params[$action['action']] = [
                    'action' => $action,
                    'params' => $hashParams,
                ];
            }
        }
        return $params;
    }
}
