<?php
namespace Grid\Source;

use Grid\Util\Traits\ExchangeArray;

use \Exception;

/**
 * Description of AbstractSource
 *
 * @author Gospodinow
 */
abstract class AbstractSource implements SourceInterface
{
    use ExchangeArray;
    
    /**
     * Query start from record
     * @var int
     */
    protected $start = 0;

    /**
     * Query ends to record
     * @var type
     */
    protected $end = 0;

    /**
     * $end - start
     * @var type
     */
    protected $limit = 0;


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

    public function __construct(array $config)
    {
        $this->exchangeArray($config);
        $this->setLimit($this->getEnd() - $this->getStart());
    }

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

    public function getLimit() : int
    {
        return $this->limit;
    }

    /**
     *
     * @param int $limit
     */
    public function setLimit(int $limit)
    {
        $this->limit = $limit;
    }
}