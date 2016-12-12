<?php

namespace Grid\Plugin;

use Grid\Util\Traits\ExchangeArray;
use Grid\Plugin\Interfaces\ColumnsPluginInterface;
use Grid\Plugin\Interfaces\DataPluginInterface;
use Grid\GridRow;

/**
 * Allows grid to have different columns for different profiles
 *
 * @author Gospodinow
 */
class ProfilePlugin extends AbstractPlugin implements ColumnsPluginInterface, DataPluginInterface
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
     * @param array $config
     * @return \self
     */
    public static function factory(array $config) : self
    {
        return new self($config);
    }

    /**
     *
     * @param array $columns
     * @return array
     */
    public function filterColumns(array $columns) : array
    {
        foreach ($columns as $key => $column) {
            if (!in_array($column->getName(), $this->columns)) {
                unset($columns[$key]);
            }
        }
        return $columns;
    }

    /**
     * Remove all fields that are not in the profile
     * @param array $data
     * @return array
     */
    public function filterData(array $data) : array
    {
        foreach ($data as $row) {
            foreach ($row as $name => $value) {
                if (!in_array($name, $this->columns)) {
                    unset($row[$name]);
                }
            }
        }
        return $data;
    }
}
