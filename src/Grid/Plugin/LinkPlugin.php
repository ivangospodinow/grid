<?php

namespace Grid\Plugin;

use Grid\Interfaces\DataPluginInterface;
use Grid\GridRow;

class LinkPlugin extends AbstractLinkPlugin implements DataPluginInterface
{
    /**
     * Column name to which link will be created
     * @var type
     */
    protected $column;

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
        $this->required('column', $config, $this);

        if (isset($config['attributes'])) {
            $this->setAttributes($config['attributes']);
            unset($config['attributes']);
        }
        $this->exchangeArray($config);
    }

    public function filterData(array $data) : array
    {
        foreach ($data as $row) {
            if (!$row->isBody()
            || !isset($row[$this->column])) {
                continue;
            }
            $row[$this->column] = $this->createLinkFromRow($row);
        }
        return $data;
    }

    public function createLinkFromRow(GridRow $row) : string
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
        if (!$this->label) {
            return '';
        }
        return sprintf(
            '%s%s%s',
            $this->beforeLabel,
            $this->getGrid()->translate($this->label),
            $this->afterLabel
        );
    }
}
