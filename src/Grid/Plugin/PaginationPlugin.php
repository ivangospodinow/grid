<?php

namespace Grid\Plugin;

use Grid\Util\Traits\ExchangeArray;
use Grid\Util\Traits\Attributes;
use Grid\Util\Traits\LinkCreatorAwareTrait;
use Grid\Interfaces\DataPluginInterface;
use Grid\Interfaces\SourcePluginInterface;
use Grid\Source\AbstractSource;
use Grid\Row\FootRow;

/**
 * Default pagination
 * To be extended for supporting bootstrap
 *
 * @author Ivan Gospodinow <ivangospodinow@gmail.com>
 */
class PaginationPlugin extends AbstractPlugin implements DataPluginInterface, SourcePluginInterface
{
    use ExchangeArray, Attributes, LinkCreatorAwareTrait;

    protected $view;

    protected $holderTagOpen        = '<div__ATTRIBUTES__>';
    protected $holderTagClose       = '</div>';
    
    protected $itemTagOpen          = '<a__ATTRIBUTES__>';
    protected $itemTagClose         = '</a>';

    protected $itemSeparator        = ' | ';

    protected $firstLabel           = '&lsaquo;&lsaquo;';
    protected $lastLabel            = '&rsaquo;&rsaquo;';

    protected $prevLabel            = '&lsaquo;';
    protected $nextLabel            = '&rsaquo;';

    protected $itemsBeforeActive    = 3;
    protected $itemsAfterActive     = 3;

    protected $summaryOpenTag       = '<span style="float:right">';
    protected $summarySeparator1    = '-';
    protected $summarySeparator2    = '/';
    protected $summaryCloseTag      = '</span>';

    protected $itemsPerPage         = 10;
    protected $showOnNoPages        = false;

    public function __construct(array $config = [])
    {
        $this->view = __DIR__ . '/../../../view/grid/plugin/pagination/pagination.phtml';
        $this->exchangeArray($config);
    }

    /**
     *
     * @param AbstractSource $source
     * @return AbstractSource
     */
    public function filterSource(AbstractSource $source) : AbstractSource
    {
        $page = $this->getLinkCreator()->getActivePaginationPage();
        if ($page > 0 && $this->itemsPerPage) {
            $source->setOffset($page * $this->itemsPerPage);
            $source->setLimit($this->itemsPerPage);
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
        $data[] = $this->getGrid()->setObjectDi(new FootRow($source));
        return $data;
    }
}
