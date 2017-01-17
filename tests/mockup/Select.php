<?php

namespace Zend\Db\Sql;

class Select
{
    protected $limit;
    protected $offset;
    protected $columns;

    public function columns($columns)
    {
        $this->columns = $columns;
    }

    public function limit($int)
    {
        $this->limit = $int;
    }

    public function offset($offset)
    {
        $this->offset = $offset;
    }

    public function getOffset()
    {
        return $this->offset;
    }

    public function getLimit()
    {
        return $this->limit;
    }

    public function reset()
    {
        
    }
}