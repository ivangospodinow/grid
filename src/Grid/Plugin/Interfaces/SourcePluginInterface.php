<?php

namespace Grid\Plugin\Interfaces;

use Grid\Source\AbstractSource;

/**
 * 
 *
 * @author Gospodinow
 */
interface SourcePluginInterface
{
    public function filterSource(AbstractSource $source) : AbstractSource;
}
