<?php

namespace Grid\Interfaces;

interface TranslateInterface
{
    /**
     *
     * @param string $string
     */
    public function translate(string $string) : string;
}
