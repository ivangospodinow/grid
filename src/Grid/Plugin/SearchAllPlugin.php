<?php

namespace Grid\Plugin;

use Grid\Row\AbstractRow;
use Grid\Row\HeadRow;
use Grid\Util\Input;
use Grid\Source\AbstractSource;

use Grid\Interfaces\DataPluginInterface;
use Grid\Interfaces\InputsInterface;
use Grid\Interfaces\SourcePluginInterface;
use Grid\Interfaces\SourceInterface;

use Grid\Util\Traits\Cache;
use Grid\Util\Traits\RowAwareTrait;
use Grid\Util\Traits\ExchangeArray;
use Grid\Util\Traits\LinkCreatorAwareTrait;
use Grid\Util\Traits\MarkMatches;

/**
 * Searching all available columns
 *
 * @author Ivan Gospodinow <ivangospodinow@gmail.com>
 */
class SearchAllPlugin extends AbstractPlugin implements DataPluginInterface, InputsInterface, SourcePluginInterface
{
    use Cache, RowAwareTrait, ExchangeArray, LinkCreatorAwareTrait, MarkMatches;

    protected $buttonName = 'searchall-submit';
    protected $buttonLabel = 'Search';

    protected $inputName = 'searchall';
    protected $inputPlaceholder = 'Seach all...';

    public function __construct(array $config = [])
    {
        $this->exchangeArray($config);
    }

    /**
     * @param array $data
     */
    public function filterData(array $data) : array
    {
        $rows = $this->getIndexRows($data, HeadRow::class, AbstractRow::DEFAULT_INDEX - 10);
        if (empty($rows)) {
            $row = $this->getGrid()->setObjectDi(
                new HeadRow('', AbstractRow::DEFAULT_INDEX - 10)
            );
            $data[] = $row;
            $rows[] = $row;
        }
        $rows[0]->setSource($rows[0]->getSource() . $this->render());

        $value = $this->getInput()->getValue();
        if ($this->markMatches && $value) {
            foreach ($this->getGrid()->getColumns() as $column) {
                if ($column->isSearchable()
                || $column->isSelectable()) {
                    $this->markMatches($column, $value, $data);
                }
            }
        }

        return $data;
    }

    /**
     *
     * @param AbstractSource $source
     * @return AbstractSource
     */
    public function filterSource(AbstractSource $source) : AbstractSource
    {
        $value = $this->getInput()->getValue();
        if ($value) {
            foreach ($this->getGrid()[SourceInterface::class] as $source) {
                foreach ($this->getGrid()->getColumns() as $column) {
                    if ($column->isSearchable()
                    || $column->isSelectable()) {
                        $source->orLike($column, $value);
                    }
                }
            }
        }
        return $source;
    }

    public function render()
    {
        $html = [];
        $html[] = sprintf(
            '<form %s>',
            'style="display:inline-block;vertical-align:middle;"'
        );
        $html[] = $this->getInput()->render();
        $html[] = $this->getButton()->render();
        $html[] = '</form>';
        return implode(PHP_EOL, $html);
    }

    public function getInput()
    {
        if (!$this->getCache(__METHOD__)) {
            $params = $this->getLinkCreator()->getParams();
            $value = $params['grid'][$this->getGrid()->getId()]['searchall'][$this->getInputName()] ?? '';
            $input = new Input(
                [
                    'name' => 'grid[' . $this->getGrid()->getId() . '][searchall][' . $this->getInputName() . ']',
                    'type' => Input::TYPE_TEXT,
                    'placeholder' => $this->getGrid()->translate($this->inputPlaceholder),
                    'value' => $value
                ]
            );
            $input->addAttribute('style', 'display:inline-block;vertical-align:middle;');
            $this->setCache(__METHOD__, $input);
        }
        return $this->getCache(__METHOD__);
    }
    
    /**
     *
     * @return Input
     */
    protected function getButton() : Input
    {
        if ($this->hasCache(__METHOD__)) {
            return $this->getCache(__METHOD__);
        }

        $input = new Input(
            [
                'name' => 'grid[' . $this->getGrid()->getId() . '][searchall][' . $this->buttonName . ']',
                'type' => Input::TYPE_BUTTON,
                'placeholder' => $this->getGrid()->translate($this->buttonLabel),
                'value' => true,
            ]
        );
        $input->addAttribute('class', ' grid-action-button');
        $input->addAttribute('style', 'visibility:hidden;position:absolute;');

        return $this->setCache(__METHOD__, $input);
    }

    /**
     *
     * @return array
     */
    public function getInputs() : array
    {
        return [$this->getInput(), $this->getButton()];
    }

    /**
     *
     * @return string
     */
    public function getInputName() : string
    {
        return (string) $this->inputName;
    }
}
