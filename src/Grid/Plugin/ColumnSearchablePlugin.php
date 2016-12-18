<?php

namespace Grid\Plugin;

use Grid\Column\AbstractColumn;
use Grid\Plugin\Interfaces\DataPluginInterface;
use Grid\Util\Traits\GridAwareTrait;
use Grid\Util\Traits\LinkCreatorAwareTrait;
use Grid\Util\Traits\ExchangeArray;
use Grid\GridInterface;
use Grid\Plugin\Interfaces\SourcePluginInterface;
use Grid\Source\AbstractSource;
use Grid\GridRow;

use Grid\Util\Traits\Cache;

use Grid\Util\Input;

/**
 *
 * @author Gospodinow
 */
class ColumnSearchablePlugin extends AbstractPlugin implements DataPluginInterface, GridInterface, SourcePluginInterface
{
    use GridAwareTrait, LinkCreatorAwareTrait, Cache, ExchangeArray;

    protected $ascLabel  = '&uarr;';
    protected $descLabel = '&darr;';

    protected $markMatches = false;
    protected $markMatchesBefore = '<u>';
    protected $markMatchesAfter = '</u>';

    public function __construct(array $config = [])
    {
        $this->exchangeArray($config);
    }

    public function filterData(array $data) : array
    {
        $hasSearchable = false;
        $source = [];
        foreach ($this->getGrid()->getColumns() as $column) {
            $source[$column->getName()] = '';
            if ($column->isSearchable()) {
                $hasSearchable = true;
                $source[$column->getName()] = $column;
            }
        }

        if ($hasSearchable) {
            foreach ($source as $name => $column) {
                if (!$column instanceof AbstractColumn) {
                    continue;
                }
                
                $value = $this->getInput($column)->getValue();
                if ($this->markMatches && $value) {
                    foreach ($data as $row) {
                        if (!$row->isBody()) {
                            continue;
                        }

                        $row[$name] = preg_replace(
                            "/({$value})/i",
                            $this->markMatchesBefore . "$1" . $this->markMatchesAfter,
                            $row[$name]
                        );
                    }
                }
                $source[$name] = $this->render($column);
            }

            $data[] = new GridRow($source, $this->getGrid(), GridRow::POSITION_HEAD);
        }
        
        return $data;
    }

    /**
     *
     * @param AbstractColumn $column
     */
    public function render(AbstractColumn $column) : string
    {
        $html = [];
        $html[] = sprintf(
            '<form action="%s" method="GET">',
            $this->getLinkCreator()->getPageBasePath()
        );
        $input = $this->getInput($column);
        $html[] = $input->render();
        $params = Input::createHiddenFromParams($this->getLinkCreator()->getParams());
        unset(
            $params[$input->getName()],
            $params[$this->getLinkCreator()->getPaginationPageName()]
        );
        $html[] = implode(PHP_EOL, $params);
        $html[] = '</form>';
        return implode(PHP_EOL, $html);
    }

    public function filterSource(AbstractSource $source) : AbstractSource
    {
        foreach ($this->getGrid()->getColumns() as $column) {
            if (!$column->isSearchable()) {
                continue;
            }

            $value = $this->getInput($column)->getValue();
            if ($value) {
                $source->orLike($column, $value);
            }
        }
        return $source;
    }

    /**
     *
     * @param AbstractColumn $column
     * @return Input
     */
    public function getInput(AbstractColumn $column) : Input
    {
        $key = spl_object_hash($column);
        if (!$this->hasCache($key)) {
            $name = sprintf(
                'grid[%s][searchable][%s]',
                $this->getGrid()->getId(),
                $column->getName()
            );
            $input = new Input(['name' => $name, 'type' => Input::TYPE_TEXT]);
            $input->setValue($this->getLinkCreator()->getFilterValue($column, 'searchable'));
            $this->setCache($key, $input);
        }
        return $this->getCache($key);
    }
}
