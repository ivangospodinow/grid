<?php

namespace Grid\Plugin\Interfaces;

/**
 *
 * @author Gospodinow
 */
interface DataPrePluginInterface
{
    public function preFilterData(array $data) : array;
}