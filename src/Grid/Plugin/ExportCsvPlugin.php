<?php

namespace Grid\Plugin;

use Grid\Interfaces\DataPluginInterface;
use Grid\Util\Traits\GridAwareTrait;
use Grid\Interfaces\GridInterface;
use Grid\Util\Traits\LinkCreatorAwareTrait;
use Grid\Util\Traits\ExchangeArray;
use Grid\Row\HeadRow;
use Grid\Row\BodyRow;
use Grid\Row\AbstractRow;
use Grid\Interfaces\InputsInterface;
use Grid\Interfaces\RenderPluginInterface;

use Grid\Util\Traits\Cache;
use Grid\Util\Traits\RowAwareTrait;

use Grid\Util\Input;

use Grid\Plugin\StripHtmlPlugin;

/**
 *
 * @author Ivan Gospodinow <ivangospodinow@gmail.com>
 */
class ExportCsvPlugin extends AbstractPlugin
implements
    DataPluginInterface,
    GridInterface,
    InputsInterface,
    RenderPluginInterface
{
    use GridAwareTrait,
        LinkCreatorAwareTrait,
        Cache,
        ExchangeArray,
        RowAwareTrait
    ;

    protected $stripHtml    = true;
    protected $buttonName  = 'grid-export-csv';
    protected $buttonLabel = 'Export CSV';
    protected $delimiter   = ',';
    protected $enclosure   = '"';
    /**
     * Available params: __GRID_ID__, __Y.m.d___
     * more?
     * @var type
     */
    protected $fileName    = 'export.__GRID_ID__.__Y.m.d___.csv';
    
    public function __construct(array $config = [])
    {
        $this->exchangeArray($config);
    }

    public function preRender(string $html) : string
    {
        $params = $this->getLinkCreator()->getParams();
        $name   = $this->getButton()->getName();
        if (isset($params[$this->getGrid()->getId()][$name])) {
            if ($this->stripHtml) {
                $this->getGrid()[] = new StripHtmlPlugin;
            }
            $this->exportCsvAndExit();
        }
        return $html;
    }

    public function postRender(string $html) : string
    {
        return $html;
    }

    public function filterData(array $data) : array
    {
        $button = $this->renderButton();
        $rows = $this->getIndexRows($data, HeadRow::class, AbstractRow::DEFAULT_INDEX - 10);
        if (empty($rows)) {
            $row = $this->getGrid()->setObjectDi(
                new HeadRow('', AbstractRow::DEFAULT_INDEX - 10)
            );
            $data[] = $row;
            $rows[] = $row;
        }
        $rows[0]->setSource($rows[0]->getSource() . $button);
        
        return $data;
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
                'name' => $this->buttonName,
                'type' => Input::TYPE_BUTTON,
                'placeholder' => $this->getGrid()->translate($this->buttonLabel),
                'value' => true,
            ]
        );
        $input->addAttribute('class', ' grid-action-button');
        return $this->setCache(__METHOD__, $input);
    }

    protected function renderButton() : string
    {
        $input = $this->getButton();
        $label = $input->getAttribute('placeholder');
        $name  = $input->getName();
        $value = $input->getValue();
        $input->removeAttribute('type');
        $input->removeAttribute('placeholder');
        $input->removeAttribute('value');
        $input->removeAttribute('name');
        
        $params = $this->getLinkCreator()->getParams();
        $params[$this->getGrid()->getId()][$name] = $value;
        return sprintf(
            '<a href="%s%s"%s>%s</a>',
            $this->getLinkCreator()->getPageBasePath(),
            empty($params) ? '' : '?' . http_build_query($params),
            $input->getAttributesString(true),
            $label
        );
    }

    protected function exportCsvAndExit()
    {
        $grid    = $this->getGrid();
        $columns = $grid->getColumns();
        $data    = $grid->getData();

        /**
         * Prevent bloking
         */
        if (session_id()) {
            session_write_close();
        }

        $name = str_replace(
            ['__GRID_ID__', '__Y.m.d___'],
            [$this->getGrid()->getId(), date('Y.m.d')],
            $this->fileName
        );

        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . $name . '"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        
        $handle = fopen('php://output', 'w');
        $headers = [];
        $exportable = [];
        foreach ($columns as $column) {
            if ($column->isExportable()) {
                $headers[] = $column->getLabel();
                $exportable[] = $column->getName();
            }
        }
        fputcsv($handle, $headers, $this->delimiter, $this->enclosure);
        
        foreach ($data as $row) {
            if ($row instanceof BodyRow) {
                $line = (array) $row;
                foreach ($line as $name => $value) {
                    if (!in_array($name, $exportable)) {
                        unset($line[$name]);
                    }
                }
                fputcsv($handle, $line, $this->delimiter, $this->enclosure);
                unset($line);
            }
        }
        exit;
    }

    /**
     *
     * @return array
     */
    public function getInputs() : array
    {
        return [$this->getButton()];
    }
}
