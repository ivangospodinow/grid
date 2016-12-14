<?php
namespace Grid\Util\Extractor;

use \Exception;

/**
 * Description of AbstractExtractor
 *
 * @author Gospodinow
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
     * @param type $key
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

        $firstCallbackNotExists = false;
        $result = null;
        foreach ($callbacks as $method) {
            if (!method_exists($source, $method)) {
                if ($result === null) {
                    $firstCallbackNotExists = true;
                }
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
        if ($firstCallbackNotExists) {
            $variables = get_object_vars($source);
            $property = $callbacks[key($callbacks)];
            if (array_key_exists($property, $variables)) {
                $result = $variables[$property];
            }
        }

        return $result;
    }
}
