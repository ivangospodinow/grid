<?php

namespace Grid\Util;

use \Exception;

/**
 * Description of StdClass
 *
 * @author Ivan Gospodinow <ivangospodinow@gmail.com>
 */
class StdClass
{
    public function __call($name, $arguments)
    {
        $action = substr($name, 0, 3);
        $property = lcfirst(substr($name, 3));
        if ($action === 'set') {
            if (!isset($arguments[0])) {
                throw new Exception('set required argument');
            }
            $this->$property = $arguments[0];
            return;
        } elseif ($action === 'get') {
            if (!isset($this->$property)) {
                throw new Exception('get required proprty to be set ' . $property);
            }
            return $this->$property;
        }

        throw new Exception('StdClass supports only get and set');
    }

    /**
     *
     * @param array $params
     * @return $this
     */
    public function exchangeArray(array $params) : self
    {
        foreach ($params as $name => $value) {
            $this->$name = $value;
        }
        return $this;
    }
}