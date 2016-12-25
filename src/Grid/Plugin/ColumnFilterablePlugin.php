<?php

namespace Grid\Plugin;

use Grid\Column\AbstractColumn;
use Grid\Plugin\Interfaces\DataPluginInterface;
use Grid\Util\Traits\GridAwareTrait;
use Grid\GridInterface;
use Grid\Util\Traits\LinkCreatorAwareTrait;
use Grid\Util\Traits\ExchangeArray;
use Grid\Plugin\Interfaces\SourcePluginInterface;
use Grid\Source\AbstractSource;
use Grid\GridRow;
use Grid\Interfaces\InputsInterface;

use Grid\Util\Traits\Cache;

use Grid\Util\Input;
/**
 *
 * @author Gospodinow
 */
class ColumnFilterablePlugin extends AbstractPlugin
implements
    DataPluginInterface,
    GridInterface,
    SourcePluginInterface,
    InputsInterface
{
    use GridAwareTrait, LinkCreatorAwareTrait, Cache, ExchangeArray;

    const TYPE_SEARCHABLE = 'searchable';
    const TYPE_SELECTABLE = 'selectable';

    protected $markMatches = false;
    protected $markMatchesBefore = '<u>';
    protected $markMatchesAfter = '</u>';
    protected $placeholder;

    public function __construct(array $config = [])
    {
        $this->exchangeArray($config);
    }
    
    public function filterData(array $data) : array
    {
        $source = [];
        foreach ($this->getGrid()->getColumns() as $column) {
            $source[$column->getName()] = '';
            
            if ($column->isSearchable()
            || $column->isSelectable()) {
                $source[$column->getName()] = $this->render($column);
                if ($this->markMatches) {
                    $data = $this->markMatchesColumn($column, $data);
                }
            }
        }

        if (!empty($source)) {
            $data[] = new GridRow($source, $this->getGrid(), GridRow::POSITION_HEAD);
        }
        
        return $data;
    }

    /**
     *
     * @param AbstractColumn $column
     * @param array $data
     * @return array
     */
    public function markMatchesColumn(AbstractColumn $column, array $data)
    {
        foreach ($this->getColumnInputs($column) as $input) {
            $value = $input->getValue();
            if (empty($value)) {
                continue;
            }
            $name  = $column->getName();
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

        $params = Input::createHiddenFromParams($this->getLinkCreator()->getParams());
        foreach ($this->getColumnInputs($column) as $input) {
            $html[] = $input->render();
            unset($params[$input->getName()]);
        }
        unset($params[$this->getLinkCreator()->getPaginationPageName()]);

        $html[] = implode(PHP_EOL, $params);
        $html[] = '</form>';
        return implode(PHP_EOL, $html);
    }

    public function filterSource(AbstractSource $source) : AbstractSource
    {
        foreach ($this->getGrid()->getColumns() as $column) {
            foreach ($this->getColumnInputs($column) as $input) {
                $value = $input->getValue();
                if ($value) {
                    $source->andLike($column, $value);
                }
            }
        }
        return $source;
    }

    public function getColumnInputs(AbstractColumn $column) : array
    {
        $key = $column->getName();
        if ($this->hasCache($key)) {
            return $this->getCache($key);
        }

        $inputs = [];
        if ($column->isSearchable()) {
            $input = new Input(
                [
                    'name' => sprintf(
                        'grid[%s][searchable][%s]',
                        $this->getGrid()->getId(),
                        $column->getName()
                    ),
                    'type' => Input::TYPE_TEXT,
                    'placeholder' => $this->placeholder ? $this->getGrid()->translate($this->placeholder) : null,
                    'value' => $this->getLinkCreator()->getFilterValue($column, 'searchable'),
                ]
            );
            $inputs[] = $input;
        }

        if ($column->isSelectable()) {
            $input = new Input(
                [
                    'name' => sprintf(
                        'grid[%s][selectable][%s]',
                        $this->getGrid()->getId(),
                        $column->getName()
                    ),
                    'type' => Input::TYPE_SELECT,
                    'placeholder' => $this->placeholder ? $this->getGrid()->translate($this->placeholder) : null,
                    'value' => $this->getLinkCreator()->getFilterValue($column, 'selectable'),
                ]
            );
            $selectableSource = $column->getSelectableSource();
            $values = [];
            if ($selectableSource === null) {
                foreach ($this->getGrid()->getObjects(AbstractSource::class) as $source) {
                    $values += $source->getColumnValues($column);
                }
            }
            $input->setValueOptions($values);
            $inputs[] = $input;
        }

        return $this->setCache($key, $inputs);
    }

    /**
     *
     * @return array
     */
    public function getInputs() : array
    {
        $inputs = [];
        foreach ($this->getGrid()->getColumns() as $column) {
            $inputs = array_merge($inputs, $this->getColumnInputs($column));
        }
        return $inputs;
    }
}
