<?php

namespace Grid\Plugin;

use Grid\Util\Traits\ExchangeArray;
use Grid\Util\Traits\Attributes;
use Grid\Util\Traits\LinkCreatorAwareTrait;
use Grid\Plugin\Interfaces\DataPluginInterface;
use Grid\Plugin\Interfaces\SourcePluginInterface;
use Grid\Source\AbstractSource;
use Grid\GridRow;

/**
 * Default pagination
 * To be extended for supporting bootstrap
 *
 * @author Gospodinow
 */
class PaginationPlugin extends AbstractPlugin implements DataPluginInterface, SourcePluginInterface
{
    use ExchangeArray, Attributes, LinkCreatorAwareTrait;

    protected $view;

    protected $holderTagOpen  = '<div__ATTRIBUTES__>';
    protected $holderTagClose = '</div>';
    
    protected $itemTagOpen    = '<a__ATTRIBUTES__>';
    protected $itemTagClose   = '</a>';

    protected $itemSeparator  = ' | ';

    protected $firstLabel     = '&lsaquo;&lsaquo;';
    protected $lastLabel      = '&rsaquo;&rsaquo;';

    protected $prevLabel     = '&lsaquo;';
    protected $nextLabel     = '&rsaquo;';

    protected $itemsBeforeActive = 3;
    protected $itemsAfterActive = 3;

    protected $itemsPerPage   = 10;
    protected $showOnNoPages  = false;

    public function __construct(array $config = [])
    {
        $this->view = __DIR__ . '/../../../view/grid/4.1.pagination.phtml';
        $this->exchangeArray($config);
    }

    /**
     *
     * @param AbstractSource $query
     * @return AbstractSource
     */
    public function filterSource(AbstractSource $source) : AbstractSource
    {
        $page = $this->getLinkCreator()->getActivePaginationPage();
        if ($page > 0 && $source->getLimit() > 0) {
            $source->setStart($page * $source->getLimit());
            $source->setEnd($page * $source->getLimit() + $source->getLimit());
        }
        return $source;
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
}
