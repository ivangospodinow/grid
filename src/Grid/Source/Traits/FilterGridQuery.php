<?php
namespace Grid\Source\Traits;

use Grid\Grid;
use Grid\Interfaces\QueryPluginInterface;

/**
 * Description of AbstractSource
 *
 * @author Ivan Gospodinow <ivangospodinow@gmail.com>
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
