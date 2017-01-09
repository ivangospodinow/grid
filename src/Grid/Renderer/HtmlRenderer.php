<?php
namespace Grid\Renderer;

use Grid\Grid;
use Grid\Util\Traits\ExchangeArray;
use Grid\Interfaces\RendererInterface;

/**
 * Description of HtmlRenderer
 *
 * @author Ivan Gospodinow <ivangospodinow@gmail.com>
 */
class HtmlRenderer implements RendererInterface
{
    use ExchangeArray;
    
    protected $open;
    protected $header;
    protected $body;
    protected $footer;
    protected $close;
    protected $grid;
    
    public function __construct(array $config = [])
    {
        $this->open   = __DIR__ . '/../../../view/grid/1.0.open.phtml';
        $this->header = __DIR__ . '/../../../view/grid/2.0.head.phtml';
        $this->body   = __DIR__ . '/../../../view/grid/3.0.body.phtml';
        $this->footer = __DIR__ . '/../../../view/grid/4.0.footer.phtml';
        $this->close  = __DIR__ . '/../../../view/grid/5.0.close.phtml';

        $this->exchangeArray($config);
    }

    public function render(Grid $grid) : string
    {
        $this->grid = $grid;
        ob_start();
        include $this->open;
        include $this->header;
        include $this->body;
        include $this->footer;
        include $this->close;
        return ob_get_clean();
    }
}