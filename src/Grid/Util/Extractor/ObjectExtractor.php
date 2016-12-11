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
    public function extract($source, $key)
    {
        if (!is_object($source)) {
            throw new Exception('Extract expects object');
        }
        if (!is_array($key)) {
            $key = [$key];
        }
        $result = null;
        foreach ($key as $method) {
            if (!method_exists($source, $method)) {
                break;
            }
            $source = call_user_func_array([$source, $method], []);
            if (!is_object($source)) {
                $result = $source;
                break;
            }
        }

        return $result;
    }
}
