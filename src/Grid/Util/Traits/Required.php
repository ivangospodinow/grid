<?php
namespace Grid\Util\Traits;

use \Exception;

/**
 * Throws exception if no array key defined
 *
 * @author Ivan Gospodinow <ivangospodinow@gmail.com>
 */
trait Required
{
    public function required($key, $array, $source)
    {
        if (!isset($array[$key])) {
            throw new Exception('Field ' . $key . ' required for ' . get_class($source));
        }
    }
}