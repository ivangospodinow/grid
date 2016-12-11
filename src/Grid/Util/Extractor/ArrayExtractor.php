<?php
namespace Grid\Util\Extractor;

use \Exception;

/**
 * Description of AbstractExtractor
 *
 * @author Gospodinow
 */
class ArrayExtractor extends AbstractExtractor
{
    /**
     * Get value from multi array
     *
     * ['key1' => ['key2' => true]
     * $key = 'key1';
     * $key = 'key1.key2'
     *
     * @param type $source
     * @param type $key
     * @return type
     * @throws Exception
     */
    public function extract($source, $key)
    {
        if (!is_array($source)) {
            throw new Exception('Extract expects array');
        }

        if (is_string($key)
        && strpos($key, '.') !== false) {
            $key = explode('.', $key);
        }
        if (!is_array($key)) {
            $key = [$key];
        }

        $result = null;
        foreach ($key as $skey) {
            if (!array_key_exists($skey, $source)) {
                break;
            }
            $source = $source[$skey];
            if (!is_array($source)) {
                $result = $source;
                break;
            }
        }
        
        return $result;
    }
}
