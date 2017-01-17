<?php

namespace Zend\Db\Sql;

class Result extends \ArrayObject
{
    public function current()
    {
        return [];
    }
}