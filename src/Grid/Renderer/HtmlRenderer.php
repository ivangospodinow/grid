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
 * Translated Grid to html
 *
 * @author Ivan Gospodinow <ivangospodinow@gmail.com>
 */
class HtmlRenderer implements RendererInterface
{
    use ExchangeArray;
    
    /**
     * <table>
     * @var type
     */
    protected $open;

    /**
     * <head>
     * @var type
     */
    protected $head;

    /**
     * <body>
     * @var type
     */
    protected $body;

    /**
     * <foot>
     * @var type
     */
    protected $foot;

    /**
     * </table>
     * @var type
     */
    protected $close;

    /**
     *
     * @var Grid\Grid
     */
    protected $grid;

    /**
     * <td data-colum="field-name"></td>
     * @var type
     */
    protected $addNamesToCells    = false;
    protected $tagNamesToCells    = 'data-column';

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
        $this->prepareData($grid);
        ob_start();
        include $this->open;
        foreach (['head', 'body', 'foot'] as $part) {
            echo '<t' . $part . '>';
            include $this->$part;
            echo '</t' . $part . '>';
        }
        include $this->close;
        return ob_get_clean();
    }

    protected function prepareData(Grid $grid)
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
    }
}