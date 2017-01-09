<?php

namespace Grid\Plugin;

use Grid\Util\Traits\GridAwareTrait;
use Grid\Interfaces\GridInterface;

/**
 * Grid aware class
 *
 * @author Gospodinow
 */
abstract class AbstractPlugin implements GridInterface
{
    use GridAwareTrait;
}
