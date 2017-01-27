<?php

namespace Grid\Util;

use Grid\Util\Traits\GridAwareTrait;
use Grid\Interfaces\GridInterface;
use Grid\Interfaces\LinksInterface;

/**
 * @author Ivan Gospodinow <ivangospodinow@gmail.com>
 */
class LinkCreator implements GridInterface
{
    use GridAwareTrait;
    
    protected $loaded = false;

    /**
     *
     * @param type $name
     * @param type $arguments
     * @return type
     */
    public function __call($name, $arguments)
    {
        if (false === $this->loaded) {
            $this->loaded = true;
            if (!isset($this->getGrid()[LinksInterface::class])) {
                $this->getGrid()[] = new Links;
            }
        }

        $result = '';
        foreach ($this->getGrid()[LinksInterface::class] as $plugin) {
            $result = call_user_func_array([$plugin, $name], $arguments);
            if (!empty($result)) {
                break;
            }
        }
        return $result;
    }
}
