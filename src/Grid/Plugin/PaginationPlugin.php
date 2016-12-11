<?php

namespace Grid\Plugin;

use Grid\Util\Traits\ExchangeArray;
use Grid\Util\Traits\Attributes;
use Grid\Plugin\Interfaces\DataPluginInterface;
use Grid\GridRow;

/**
 * Default pagination
 * To be extended for supporting bootstrap
 *
 * @author Gospodinow
 */
class PaginationPlugin extends AbstractPlugin implements DataPluginInterface
{
    use ExchangeArray, Attributes;

    protected $view;

    protected $holderTagOpen  = '<div__ATTRIBUTES__>';
    protected $holderTagClose = '</div>';
    
    protected $itemTagOpen    = '<a__ATTRIBUTES__>';
    protected $itemTagClose   = '</a>';

    protected $itemSeparator  = ' | ';

    protected $firstLabel     = '&lt;-|';
    protected $lastLabel      = '|-&gt;';

    protected $itemsPerPage   = 10;
    protected $showOnNoPages  = true;

    /**
     *
     * @var PaginationLinkInterface
     */
    protected $linkCreator;

    public function __construct(array $config = [])
    {
        $this->view = __DIR__ . '/../../../view/grid/4.1.pagination.phtml';
        $this->exchangeArray($config);
    }

    /**
     *
     * @param array $data
     * @return array
     */
    public function filterData(array $data) : array
    {
        ob_start();
        include $this->view;
        $source = ob_get_clean();
        $data[] = new GridRow(
            $source,
            $this->getGrid(),
            GridRow::POSITION_FOOTER
        );
        return $data;
    }

    /**
     *
     * @return \Grid\Plugin\PaginationLinkInterface
     */
    public function getLinkCreator() : PaginationLinkInterface
    {
        if (!$this->linkCreator) {
            $this->setLinkCreator(new PaginationLink);
        }
        $this->linkCreator->setGrid($this->getGrid());
        return $this->linkCreator;
    }

    /**
     *
     * @param \Grid\Plugin\PaginationLinkInterface $creator
     * @return \self
     */
    public function setLinkCreator(PaginationLinkInterface $creator) : self
    {
        $this->linkCreator = $creator;
        return $this;
    }
}