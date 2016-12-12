<?php

namespace Grid\Source\Interfaces;

/**
 * $query can be string, array, object
 * depending on the implementation
 * for example: Pdo, Doctrine and etc
 *
 * @author Gospodinow
 */
interface QuerySourceInterface
{
    public function getQuery();
    public function setQuery($query);
}