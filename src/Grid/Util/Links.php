<?php

namespace Grid\Util;

use Grid\Util\Traits\GridAwareTrait;
use Grid\Util\Traits\ExchangeArray;
use Grid\GridInterface;
use Grid\Column\AbstractColumn;
use Grid\Interfaces\LinksInterface;

/**
 *
 * @author Gospodinow
 */
class Links implements GridInterface, LinksInterface
{
    use GridAwareTrait, ExchangeArray;

    protected $params = [];

    public function __construct(array $config = [])
    {
        if (!isset($config['params'])) {
            $config['params'] = $_GET;
        }
        $this->exchangeArray($config);
    }

    /**
     *
     * @param AbstractColumn $column
     * @param string $type
     * @param string $value
     * @return string
     */
    public function createFilterLink(AbstractColumn $column, string $type, string $value) : string
    {
        $params = $this->params;
        $params['grid'][$this->getGrid()->getId()][$type][$column->getName()] = $value;
        return $this->createQueryLink($params);
    }

    /**
     *
     * @param AbstractColumn $column
     * @param string $type
     * @param string $default
     * @return string
     */
    public function getFilterValue(AbstractColumn $column, string $type, string $default = '') : string
    {
        if (isset($this->params['grid'][$this->getGrid()->getId()][$type][$column->getName()])) {
            return $this->params['grid'][$this->getGrid()->getId()][$type][$column->getName()];
        }
        return $default;
    }

    /**
     * ?grid[grid-id][page]=1
     * @param int $page
     * @return string
     */
    public function createPaginationLink(int $page) : string
    {
        $params = $this->params;
        if ($page === 0) {
            unset($params['grid'][$this->getGrid()->getId()]['page']);
        } else {
            $params['grid'][$this->getGrid()->getId()]['page'] = $page;
        }

        return $this->createQueryLink($params);
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

    /**
     *
     * @param array $params
     * @return string
     */
    protected function createQueryLink(array $params) : string
    {
        if (isset($params['grid'])) {
            foreach ($params['grid'] as $name => $value) {
                if (empty($value)) {
                    unset($params['grid'][$name]);
                }
            }
            if (empty($params['grid'])) {
                unset($params['grid']);
            }
        }
        
        return count($params) > 0
             ? '?' . http_build_query($params)
             : strstr($_SERVER['REQUEST_URI'], '?', true);
    }
}