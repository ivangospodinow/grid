<?php

namespace Grid\Plugin;

use Grid\Util\Traits\ExchangeArray;
use Grid\Plugin\Interfaces\ColumnsPluginInterface;
use Grid\Plugin\Interfaces\DataPluginInterface;

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

    /**
     * Remove all fields that are not in the profile
     * @param array $data
     * @return array
     */
    public function filterData(array $data) : array
    {
        /**
         * If the default plugin is available
         */
        if (!isset($this->getGrid()[ColumnsOnlyDataPlugin::class])) {
            $plugin = new ColumnsOnlyDataPlugin;
            $this->getGrid()->setObjectDi($plugin);
            $data = $plugin->filterData($data);
            unset($plugin);
        }
        
        return $data;
    }
}
