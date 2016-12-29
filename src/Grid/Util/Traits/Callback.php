<?php
namespace Grid\Util\Traits;

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
        if (is_array($callback)
        && !is_object($callback[0])) {
            $callback[0] = new $callback[0];
        }
        return call_user_func_array($callback, $params);
    }
}
