<?php

namespace Grid\Plugin;

use Grid\Interfaces\DataPluginInterface;
use Grid\Row\AbstractRow;
use Grid\Row\BodyRow;
use Grid\Row\HeadRow;

use Grid\Util\Traits\RowAwareTrait;

use \Exception;

/**
 * @author Ivan Gospodinow <ivangospodinow@gmail.com>
 */
class LinkPlugin extends AbstractLinkPlugin implements DataPluginInterface
{
    use RowAwareTrait;
    
    /**
     * Column name to which link will be created
     * Optional parameter
     * @var type
     */
    protected $column;

    /**
     * Row index to which to append the action
     * Optional parameter
     * @var type
     */
    protected $rowIndex;

    /**
     * If index and row class are available,
     * then add the link to the row
     * @var type
     */
    protected $rowClass = HeadRow::class;

    /**
     * Relative path from /
     * @example /users/edit/:id
     * @var type
     */
    protected $uri;

    /**
     * array for extracts
     * ['id' => 'getId']
     * @var type 
     */
    protected $uriParameters = [];

    /**
     * <span>Label</span>
     * @var type
     */
    protected $beforeLabel;
    protected $label;
    protected $afterLabel;

    public function __construct(array $config)
    {
        if (isset($config['attributes'])) {
            $this->setAttributes($config['attributes']);
            unset($config['attributes']);
        }
        $this->exchangeArray($config);
        
        if (!$this->column && !$this->rowIndex) {
            throw new Exception('Column or rowIndex required.');
        }
    }

    public function filterData(array $data) : array
    {
        /**
         * Adds link in column cell
         */
        if (null !== $this->column) {
            foreach ($data as $row) {
                if (!$row instanceof BodyRow
                || !isset($row[$this->column])) {
                    continue;
                }
                $row[$this->column] = $this->createLinkFromRow($row);
            }
        }

        /**
         * Append link to grid row
         */
        if (null !== $this->rowIndex
        && class_exists($this->rowClass)) {
            $rows = $this->getIndexRows($data, $this->rowClass, $this->rowIndex);
            if (empty($rows)) {
                $class = $this->rowClass;
                $row = $this->getGrid()->setObjectDi(new $class('', $this->rowIndex));
                $data[] = $row;
                $rows[] = $row;
            }
            $rows[0]->setSource(
                sprintf(
                    '%s%s',
                    $rows[0]->getSource(),
                    $this->createLink(
                        [],
                        $this->uri,
                        $this->uriParameters,
                        $this->getLabel(),
                        $this->getAttributes()
                    )
                )
            );
        }

        return $data;
    }
    
    public function createLinkFromRow(AbstractRow $row) : string
    {
        return
        $this->createLink(
            $row->getSource(),
            $this->uri,
            $this->uriParameters,
            $this->label ? $this->getLabel() : isset($row[$this->column]) ? $row[$this->column] : '',
            $this->getAttributes()
        );
    }

    public function getLabel() : string
    {
        return sprintf(
            '%s%s%s',
            $this->beforeLabel,
            $this->label ? $this->getGrid()->translate($this->label) : $this->label,
            $this->afterLabel
        );
    }
}
