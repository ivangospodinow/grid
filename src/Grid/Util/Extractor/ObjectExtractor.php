<?php
namespace Grid\Util\Extractor;

use \Exception;

/**
 * Description of AbstractExtractor
 *
 * @author Ivan Gospodinow <ivangospodinow@gmail.com>
 */
class ObjectExtractor extends AbstractExtractor
{
    /**
     * Calls multiple callbacks from object
     *
     * $key = 'getName';
     * $key = ['getUser', 'getName']
     *
     * @param type $source
     * @param type $callback
     * @return type
     * @throws Exception
     */
    public function extract($source, $callback)
    {
        if (!is_object($source)) {
            throw new Exception('Extract expects object');
        }
        if (!is_array($callback)) {
            $callbacks = [$callback];
        } else {
            $callbacks = $callback;
        }

        $hasCall = method_exists($source, '__call');
        
        $result = null;
        foreach ($callbacks as $method) {

            if (!method_exists($source, $method)
            && (!$hasCall || substr($method, 0, 3) !== 'get')) {
                break;
            }

            $source = call_user_func_array([$source, $method], []);
            if (!is_object($source)) {
                $result = $source;
                break;
            }
        }

        /**
         * In case of public property access
         * $user->name;
         */
        if (null === $result && is_object($source)) {
            $variables = get_object_vars($source);
            $property = $callbacks[key($callbacks)];
            if (array_key_exists($property, $variables)) {
                $result = $variables[$property];
            }
        }

        return $result;
    }
}
