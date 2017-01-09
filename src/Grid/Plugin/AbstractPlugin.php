<?php

namespace Grid\Plugin;

use Grid\Util\Traits\GridAwareTrait;
use Grid\Interfaces\GridInterface;

/**
 * Grid aware class
 *
 * @author Ivan Gospodinow <ivangospodinow@gmail.com>
 */
abstract class AbstractPlugin implements GridInterface
{
    use GridAwareTrait;
}
