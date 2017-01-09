<?php
namespace Grid\Source\Traits;

use Grid\Grid;
use Grid\Interfaces\QueryPluginInterface;

/**
 * Description of AbstractSource
 *
 * @author Gospodinow
 */
trait FilterGridQuery
{
    public function filterGridQuery(Grid $grid, $query)
    {
        foreach ($grid[QueryPluginInterface::class] as $plugin) {
            $query = $plugin->filterQuery($query);
        }
        return $query;
    }
}
