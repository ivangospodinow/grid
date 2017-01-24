<?php

namespace Grid\Plugin;

use Grid\Interfaces\DataPluginInterface;
use Grid\Row\HeadRow;
use Grid\Row\FootRow;
use Grid\Util\Traits\ExchangeArray;

/**
 * Creating table headers
 *
 * @author Ivan Gospodinow <ivangospodinow@gmail.com>
 */
class HeaderPlugin extends AbstractPlugin implements DataPluginInterface
{
    use ExchangeArray;

    const POSITION_HEAD = 'head';
    const POSITION_FOOT = 'foot';
    const POSITION_BOTH = 'both';
    
    protected $position;

    public function __construct(array $config = [])
    {
        if (!isset($config['position'])) {
            $config['position'] = self::POSITION_HEAD;
        }
        if (!in_array($config['position'], [self::POSITION_BOTH, self::POSITION_FOOT, self::POSITION_HEAD])) {
            $config['position'] = self::POSITION_HEAD;
        }
        $this->exchangeArray($config);
    }

        /**
     * gets the column value from source
     * 
     * @param array $data
     * @return AbstractRow
     */
    public function filterData(array $data) : array
    {
        $headers = [];
        foreach ($this->getGrid()->getColumns() as $column) {
            $headers[$column->getName()] = sprintf(
                '%s%s%s',
                $column->getPreLabel(),
                $column->getLabel(),
                $column->getPostLabel()
            );
        }

        if ($this->position === self::POSITION_BOTH
        || $this->position === self::POSITION_HEAD) {
            array_unshift(
                $data,
                $this->getGrid()->setObjectDi(new HeadRow($headers))
            );
        }

        if ($this->position === self::POSITION_BOTH
        || $this->position === self::POSITION_FOOT) {
            array_unshift(
                $data,
                $this->getGrid()->setObjectDi(new FootRow($headers))
            );
        }
        
        return $data;
    }
}
