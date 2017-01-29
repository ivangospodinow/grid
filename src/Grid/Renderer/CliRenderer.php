<?php
namespace Grid\Renderer;

use Grid\Grid;
use Grid\Plugin\StripHtmlPlugin;
use Grid\Interfaces\RendererInterface;

use Grid\Row\HeadRow;
use Grid\Row\BodyRow;

use cli\Table;

/**
 * Description of CliRenderer
 *
 * @author Ivan Gospodinow <ivangospodinow@gmail.com>
 */
class CliRenderer implements RendererInterface
{
    public function render(Grid $grid) : string
    {
        $grid[] = new StripHtmlPlugin;
        $headers = [];
        foreach ($grid->getColumns() as $column) {
            $headers[] = $column->getLabel();
        }
        $rows = [];
        foreach ($grid->getData() as $key => $row) {
            if ($row instanceof BodyRow) {
                $rows[] = (array) $row;
            }
        }
        
        $table = new Table($headers, $rows);
        return implode(PHP_EOL, $table->getDisplayLines());
    }
}