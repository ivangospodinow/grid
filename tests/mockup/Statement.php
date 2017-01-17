<?php

namespace Zend\Db\Sql;

class Statement
{
    public function execute()
    {
        return new Result([['id' => 1], ['id' => 2]]);
    }
}