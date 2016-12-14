<?php

namespace Grid\Hydrator;

use Grid\Plugin\Interfaces\HidratorPluginInterface;
use Grid\Util\StdClass;
use Grid\Util\Traits\GridAwareTrait;
use Grid\GridInterface;

use \Exception;

class Hydrator implements HydratorInterface, GridInterface, HidratorPluginInterface
{
    use GridAwareTrait;
    
    public function hydrate($data)
    {
        if (is_array($data)) {
            $object = new StdClass;
            $object->exchangeArray($data);
            return $object;
        } elseif (is_object($data)) {
            if ($data instanceof GridInterface) {
                $data->setGrid($this->getGrid());
            }
            return $data;
        }

        throw new Exception('Hydrator supports only array or object as $data');
    }
}
