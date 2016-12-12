<?php
namespace Grid\Source\Traits;

use Grid\Grid;
use Grid\Plugin\Interfaces\QueryPluginInterface;

/**
 * Description of AbstractSource
 *
 * @author Gospodinow
 */
trait FilterGridQuery
{
    public function filterGridQuery(Grid $grid, $query)
    {
        $plugins = $grid->getObjects(QueryPluginInterface::class);
        foreach ($plugins as $plugin) {
            $query = $plugin->filterQuery($query);
        }
        return $query;
    }
}
