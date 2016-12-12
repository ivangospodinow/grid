<?php
namespace Grid\Source;

use \Exception;

/**
 * Description of AbstractSource
 *
 * @author Gospodinow
 */
abstract class AbstractSource implements SourceInterface
{
    /**
     * Query start from record
     * @var int
     */
    protected $start;

    /**
     * Query ends to record
     * @var type
     */
    protected $end;
    
    /**
     * Resultset count
     * @var type 
     */
    protected $count;

    /**
     * Resultset
     * @var type
     */
    protected $rows;

    public static function factory(array $config) : SourceInterface
    {
        if (!isset($config['type'])) {
            throw new Exception('Type is required');
        }

        if ($config['type'] === 'array') {
            return  new ArraySource(isset($config['data']) ? $config['data'] : []);
        }

        throw new Exception('Invalid factory type');
    }

    /**
     *
     * @param int $start
     */
    public function setStart(int $start)
    {
        $this->start = $start;
    }

    /**
     *
     * @param int $end
     */
    public function setEnd(int $end)
    {
        $this->end = $end;
    }

    /**
     *
     * @return type
     */
    public function getStart() : int
    {
        return $this->start;
    }

    /**
     *
     * @return int
     */
    public function getEnd() : int
    {
        return $this->end;
    }

}