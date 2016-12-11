<?php
namespace Grid\Util\Traits;

/**
 * Description of ParamsSetter
 *
 * @author Gospodinow
 */
trait ExchangeArray
{
    /**
     *
     * @param array $params
     * @return $this
     */
    public function exchangeArray(array $params)
    {
        foreach ($params as $name => $value) {
            $setter = 'set' . ucfirst($name);
            if (method_exists($this, $setter)) {
                $this->$setter($value);
            } else if (property_exists($this, $name)) {
                $this->$name = $value;
            }
        }
        return $this;
    }
}