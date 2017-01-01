<?php
namespace Grid\Util\Traits;

use \Exception;

/**
 *
 * @author Gospodinow
 */
trait Callback
{
    /**
     *
     * @param callback $callback
     * @param type $params
     * @return type
     */
    public function call_user_func_array($callback, $params)
    {
        if (is_array($callback)) {
            if (!isset($callback[0])
             || !isset($callback[1])) {
                 throw new Exception('callback must have 0=>object,class 1=> method');
             }
             
            if (!is_object($callback[0])) {
                $callback[0] = new $callback[0];
            }
        }

        return call_user_func_array($callback, $params);
    }
}
