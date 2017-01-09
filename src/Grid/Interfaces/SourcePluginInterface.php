<?php

namespace Grid\Interfaces;

use Grid\Source\AbstractSource;

/**
 * 
 *
 * @author Ivan Gospodinow <ivangospodinow@gmail.com>
 */
interface SourcePluginInterface
{
    public function filterSource(AbstractSource $source) : AbstractSource;
}
