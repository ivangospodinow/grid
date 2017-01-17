<?php

namespace Zend\Db\Sql;

class Sql
{
    public function select($table)
    {
        return new \Zend\Db\Sql\Select;
    }

    public function prepareStatementForSqlObject($select)
    {
        return new Statement();
    }
}