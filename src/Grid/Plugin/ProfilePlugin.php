<?php

namespace Grid\Plugin;

use Grid\Util\Traits\ExchangeArray;
use Grid\Interfaces\ColumnsPluginInterface;

/**
 * Allows grid to have different columns for different profiles
 *
 * @author Ivan Gospodinow <ivangospodinow@gmail.com>
 */
class ProfilePlugin extends AbstractPlugin implements ColumnsPluginInterface
{
    use ExchangeArray;
    
    /**
     * [column1, column2 ...]
     * @var type
     */
    protected $columns = [];
    
    public function __construct(array $config)
    {
        $this->exchangeArray($config);
    }
    
    /**
     *
     * @param array $columns
     * @return array
     */
    public function filterColumns(array $columns) : array
    {
        $sorted = [];
        foreach ($this->columns as $name) {
            foreach ($columns as $column) {
                if ($column->getName() === $name) {
                    $sorted[] = $column;
                }
            }
        }
        unset($columns);
        return $sorted;
    }
}
