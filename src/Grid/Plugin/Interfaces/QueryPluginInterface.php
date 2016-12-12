<?php

namespace Grid\Plugin\Interfaces;

/**
 * $query can be string, array, object
 * depending on the implementation
 * for example: Pdo, Doctrine and etc
 *
 * @author Gospodinow
 */
interface QueryPluginInterface
{
    public function filterQuery($query);
}
