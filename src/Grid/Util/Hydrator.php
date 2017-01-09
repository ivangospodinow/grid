<?php

namespace Grid\Util;

use Grid\Util\StdClass;
use Grid\Util\Traits\GridAwareTrait;
use Grid\Interfaces\GridInterface;
use Grid\Interfaces\HydratorInterface;

use \Exception;

class Hydrator implements GridInterface, HydratorInterface
{
    use GridAwareTrait;
    
    public function hydrate($data)
    {
        if (is_array($data)) {
            $object = new StdClass;
            $object->exchangeArray($data);
            return $object;
        } elseif (is_object($data)) {
            $this->getGrid()->setObjectDi($data);
            return $data;
        }

        throw new Exception('Hydrator supports only array or object as $data');
    }
}
