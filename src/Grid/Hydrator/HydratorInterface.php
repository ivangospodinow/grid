<?php

namespace Grid\Hydrator;

interface HydratorInterface
{
    /**
     * The purpose of this interface is
     * to transform data into object
     * with business logic
     * @example return new \App\Entity\User($data);
     * @param array | object $data
     */
    public function hydrate($data);
}
