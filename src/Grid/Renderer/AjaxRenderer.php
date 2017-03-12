<?php
namespace Grid\Renderer;

use Grid\Grid;

/**
 * Translated Grid to html in json
 *
 * @author Ivan Gospodinow <ivangospodinow@gmail.com>
 */
class AjaxRenderer extends HtmlRenderer
{
    public function render(Grid $grid) : string
    {
        $this->prepareData($grid);
        $data = [];
        $data['id'] = $grid->getId();
        foreach (['head', 'body', 'foot'] as $part) {
            ob_start();
            include $this->$part;
            $data['t' . $part] = ob_get_clean();
        }
        return json_encode($data);
    }
}