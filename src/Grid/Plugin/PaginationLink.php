<?php

namespace Grid\Plugin;

use Grid\Util\Traits\GridAwareTrait;
use Grid\GridInterface;

/**
 * Pagination link provider
 * @TODO move else where
 *
 * @author Gospodinow
 */
class PaginationLink implements PaginationLinkInterface, GridInterface
{
    use GridAwareTrait;

    protected $params;

    public function __construct(array $get = [])
    {
        if (empty($get)) {
            $get = $_GET;
        }
        $this->params = $get;
    }

    /**
     * ?grid[grid-id][page]=1
     * @param int $page
     * @return string
     */
    public function createPaginationLink(int $page) : string
    {
        $params = $this->params;
        $params['grid'] = [$this->getGrid()->getId() => ['page' => $page]];
        return '?' . http_build_query($params);
    }

    /**
     *
     * @return int
     */
    public function getActivePaginationPage() : int
    {
        if (isset($this->params['grid'][$this->getGrid()->getId()]['page'])) {
            return (int) $this->params['grid'][$this->getGrid()->getId()]['page'];
        }
        return 0;
    }
}