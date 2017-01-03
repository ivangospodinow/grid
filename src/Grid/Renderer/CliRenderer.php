<?php
namespace Grid\Renderer;

use Grid\Grid;
use Grid\Plugin\StripHtmlPlugin;

use cli\Table;

/**
 * Description of CliRenderer
 *
 * @author Gospodinow
 */
class CliRenderer implements RendererInterface
{
    public function render(Grid $grid) : string
    {
        $grid[] = new StripHtmlPlugin;
        
        $data = $grid->getData();

        $headers = [];
        $rows = [];
        foreach ($data as $key => $row) {
            if ($row->isHead()) {
                $headers = (array) $row;
            } elseif ($row->isBody()) {
                $rows[] = (array) $row;
            }
        }
        
        $table = new Table($headers, $rows);
        return implode(PHP_EOL, $table->getDisplayLines());
    }
}