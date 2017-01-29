<?php

namespace Grid\Row;

use Grid\Util\Traits\Attributes;

use \ArrayObject;

/**
 * Holds all related data for each row
 *
 * @author Ivan Gospodinow <ivangospodinow@gmail.com>
 */
abstract class AbstractRow extends ArrayObject
{
    use Attributes;

    const DEFAULT_INDEX = 100;

    /**
     * Can be object, array or string, useful info ah ?
     * @var type
     */
    protected $source;

    /**
     * Number of row position, lower first
     * @var type
     */
    protected $index;

    /**
     *
     * @param string | array | object [__toString, ArrayAccess] $source
     * @param string $index
     */
    public function __construct($source, int $index = self::DEFAULT_INDEX)
    {
        if (is_array($source)) {
            parent::__construct($source);
        }
        
        $this->setSource($source);
        $this->index    = $index;
    }

    /**
     *
     * @return type
     */
    public function getSource()
    {
        return $this->source;
    }

    /**
     *
     * @param string | array $source
     */
    public function setSource($source)
    {
        $this->source = $source;
    }

    /**
     *
     * @return int
     */
    public function getIndex() : int
    {
        return $this->index;
    }

    /**
     *
     * @return bool
     */
    public function isString() : bool
    {
        return is_string($this->source)
            || (is_object($this->source) && method_exists($this->source, '__toString'));
    }
}
