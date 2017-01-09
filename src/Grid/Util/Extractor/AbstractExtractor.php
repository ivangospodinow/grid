<?php
namespace Grid\Util\Extractor;

use \Exception;

/**
 * Description of AbstractExtractor
 *
 * @author Ivan Gospodinow <ivangospodinow@gmail.com>
 */
abstract class AbstractExtractor
{
    abstract public function extract($source, $key);

    /**
     *
     * @param type $source
     * @return AbstractExtractor
     * @throws Exception
     */
    public static function factory($source) : AbstractExtractor
    {
        if (is_object($source)) {
            return new ObjectExtractor;
        } elseif (is_array($source)) {
            return new ArrayExtractor;
        }

        throw new Exception('Invald source for extractor');
    }
}
