<?php

namespace Grid\Interfaces;

/**
 * @author Ivan Gospodinow <ivangospodinow@gmail.com>
 */
interface TranslateInterface
{
    /**
     *
     * @param string $string
     */
    public function translate(string $string) : string;
}
