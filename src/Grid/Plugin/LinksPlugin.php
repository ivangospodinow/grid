<?php

namespace Grid\Plugin;

use Grid\Interfaces\DataPluginInterface;

/**
 * @author Ivan Gospodinow <ivangospodinow@gmail.com>
 */
class LinksPlugin extends AbstractLinkPlugin implements DataPluginInterface
{
    /**
     *
     * @var string
     */
    protected $column;
    protected $links = [];

    public function __construct(array $config)
    {
        $this->required('column', $config, $this);
        $this->required('links', $config, $this);
        
        $this->exchangeArray($config);
    }

    public function filterData(array $data) : array
    {
        
        foreach ($data as $row) {
            if (!$row->isBody()) {
                continue;
            }
            $linkTags = [];
            $links = $this->getLinks();
            foreach ($links as $link) {
                $linkTags[] = $link->createLinkFromRow($row);
            }

            $row[$this->column] = implode(PHP_EOL, $linkTags);
        }
        return $data;
    }

    /**
     *
     * @return array
     */
    public function getLinks() : array
    {
        $links = [];
        foreach ($this->links as $link) {
            $link['column'] = $this->column;
            $links[] = $this->getGrid()->setObjectDi(new LinkPlugin($link));
        }
        return $links;
    }
}
