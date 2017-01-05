<?php

namespace Grid;

use Grid\Util\Traits\Attributes;
use Grid\Util\Traits\GridAwareTrait;
use Grid\GridInterface;

use \ArrayObject;

/**
 * Description of Grid
 *
 * @author Gospodinow
 */
class GridRow extends ArrayObject implements GridInterface
{
    use Attributes, GridAwareTrait;
    
    const POSITION_HEAD   = 'head';
    const POSITION_BODY   = 'body';
    const POSITION_FOOTER = 'foot';

    /**
     * Can be object, array or string, useful info ah ?
     * @var type
     */
    protected $source;

    /**
     *
     * @var type Grid
     */
    protected $grid;

    /**
     *
     * @var string
     */
    protected $position = self::POSITION_BODY;
    
    /**
     *
     * @param string | array | object [__toString, ArrayAccess] $source
     * @param \Grid\Grid $grid
     * @param string $position
     */
    public function __construct($source, Grid $grid, string $position = self::POSITION_BODY)
    {
        if (is_array($source)) {
            parent::__construct($source);
        }
        
        $this->source   = $source;
        $this->grid     = $grid;
        $this->position = $position;
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
     * @return string
     */
    public function getPosition() : string
    {
        return $this->position;
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

    /**
     *
     * @return bool
     */
    public function isHead() : bool
    {
        return $this->getPosition() === self::POSITION_HEAD;
    }

    /**
     *
     * @return bool
     */
    public function isBody() : bool
    {
        return $this->getPosition() === self::POSITION_BODY;
    }

    /**
     *
     * @return bool
     */
    public function isFoot() : bool
    {
        return $this->getPosition() === self::POSITION_FOOTER;
    }
}
