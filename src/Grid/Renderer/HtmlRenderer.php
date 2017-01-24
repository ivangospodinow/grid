<?php
namespace Grid\Renderer;

use Grid\Grid;
use Grid\Util\Traits\ExchangeArray;
use Grid\Interfaces\RendererInterface;

use Grid\Row\AbstractRow;
use Grid\Row\HeadRow;
use Grid\Row\BodyRow;
use Grid\Row\FootRow;

/**
 * Description of HtmlRenderer
 *
 * @author Ivan Gospodinow <ivangospodinow@gmail.com>
 */
class HtmlRenderer implements RendererInterface
{
    use ExchangeArray;

    protected $renderParts = [
        'open',
        'head',
        'body',
        'foot',
        'close'
    ];

    protected $open;
    
    protected $head;
    protected $body;
    protected $foot;
    
    protected $close;
    
    protected $grid;
    
    public function __construct(array $config = [])
    {
        $this->open         = __DIR__ . '/../../../view/grid/table/1.0.open.phtml';
        $this->head         = __DIR__ . '/../../../view/grid/table/2.0.head.phtml';
        $this->body         = __DIR__ . '/../../../view/grid/table/3.0.body.phtml';
        $this->foot         = __DIR__ . '/../../../view/grid/table/4.0.foot.phtml';
        $this->close        = __DIR__ . '/../../../view/grid/table/5.0.close.phtml';

        $this->exchangeArray($config);
    }

    public function render(Grid $grid) : string
    {
        $this->grid   = $grid;
        $data         = $grid->getData();
        $rows['head'] = [];
        $rows['body'] = [];
        $rows['foot'] = [];

        foreach ($data as $row) {
            $row->setAttribute('data-index', $row->getIndex());
            if ($row instanceof BodyRow) {
                $rows['body'][$row->getIndex()][] = $row;
            } else if ($row instanceof HeadRow) {
                $rows['head'][$row->getIndex()][] = $row;
            } else if ($row instanceof FootRow) {
                $rows['foot'][$row->getIndex()][] = $row;
            }
        }

        foreach ($rows as &$grouped) {
            ksort($grouped);
        }
        
        $this->rows = $rows;
        ob_start();
        foreach ($this->renderParts as $part) {
            include $this->$part;
        }
        return ob_get_clean();
    }
}