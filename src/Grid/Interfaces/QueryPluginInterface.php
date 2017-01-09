<?php

namespace Grid\Interfaces;

/**
 * $query can be string, array, object
 * depending on the implementation
 * for example: Pdo, Doctrine and etc
 *
 * @author Ivan Gospodinow <ivangospodinow@gmail.com>
 */
interface QueryPluginInterface
{
    public function filterQuery($query);
}
