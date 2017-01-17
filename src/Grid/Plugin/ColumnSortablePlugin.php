<?php

namespace Grid\Plugin;

use Grid\Column\AbstractColumn;
use Grid\Interfaces\RenderPluginInterface;
use Grid\Util\Traits\GridAwareTrait;
use Grid\Util\Traits\LinkCreatorAwareTrait;;
use Grid\Interfaces\GridInterface;
use Grid\Interfaces\SourcePluginInterface;
use Grid\Source\AbstractSource;
use Grid\Grid;

/**
 *
 * @author Ivan Gospodinow <ivangospodinow@gmail.com>
 */
class ColumnSortablePlugin extends AbstractPlugin implements RenderPluginInterface, GridInterface, SourcePluginInterface
{
    use GridAwareTrait, LinkCreatorAwareTrait;

    protected $ascLabel  = '&uarr;';
    protected $descLabel = '&darr;';

    /**
     *
     * @param string $html
     */
    public function preRender(string $html) : string
    {
        foreach ($this->getGrid()->getColumns() as $column) {
            if ($column->isSortable()) {
                $this->render($column);
            }
        }
        return $html;
    }

    /**
     *
     * @param string $html
     * @param Grid $grid
     * @return string
     */
    public function postRender(string $html) : string
    {
        return $html;
    }

    /**
     *
     * @param AbstractColumn $column
     */
    public function render(AbstractColumn $column)
    {
        $link = $this->getLinkCreator();
        $value = strtoupper($link->getFilterValue($column, 'sortable'));
        $direction = $value === 'ASC' ? 'desc' : 'asc';
        $url = $link->createFilterLink($column, 'sortable', $direction);
        $column->setPreLabel('<a href="' . $url . '">');
        if ($value && $value === 'ASC') {
            $column->setPostLabel(' ' . $this->ascLabel . '</a>');
        } elseif ($value && $value === 'DESC') {
            $column->setPostLabel(' ' . $this->descLabel . '</a>');
        }
    }

    public function filterSource(AbstractSource $source) : AbstractSource
    {
        $link = $this->getLinkCreator();
        $order = [];
        foreach ($this->getGrid()->getColumns() as $column) {
            if (!$column->isSortable()) {
                continue;
            }

            $value = $link->getFilterValue($column, 'sortable');
            if ($value) {
                $order[$column->getName()] = strtoupper($value) === 'ASC' ? 'ASC' : 'DESC';
            }
        }
        
        if (!empty($order)) {
            $source->setOrder($order);
        }

        return $source;
    }
}
