<?php

namespace Grid\Interfaces;

interface InputsAwareTrait
{
    /**
     *
     * @param string $string
     */
    public function getInputs() : array;
}
