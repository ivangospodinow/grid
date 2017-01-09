<?php

namespace Grid\Plugin;

use Grid\Interfaces\DataPluginInterface;
use Grid\GridRow;
use Grid\Util\Traits\ExchangeArray;

/**
 * Creating table headers
 *
 * @author Gospodinow
 */
class HeaderPlugin extends AbstractPlugin implements DataPluginInterface
{
    use ExchangeArray;

    const POSITION_TOP    = 'top';
    const POSITION_BOTTOM = 'bottom';
    const POSITION_BOTH   = 'both';
    
    protected $position;

    public function __construct(array $config = [])
    {
        if (!isset($config['position'])) {
            $config['position'] = self::POSITION_TOP;
        }
        if (!in_array($config['position'], [self::POSITION_BOTH, self::POSITION_BOTTOM, self::POSITION_TOP])) {
            $config['position'] = self::POSITION_TOP;
        }
        $this->exchangeArray($config);
    }

        /**
     * gets the column value from source
     * 
     * @param array $data
     * @return GridRow
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
        || $this->position === self::POSITION_TOP) {
            array_unshift(
                $data,
                $this->getGrid()->setObjectDi(
                    new GridRow(
                        $headers,
                        GridRow::POSITION_HEAD
                    )
                )
            );
        }

        if ($this->position === self::POSITION_BOTH
        || $this->position === self::POSITION_BOTTOM) {
            array_unshift(
                $data,
                $this->getGrid()->setObjectDi(
                    new GridRow(
                        $headers,
                        GridRow::POSITION_FOOTER
                    )
                )
            );
        }
        
        return $data;
    }
}