<?php
namespace Grid\Util\Traits;

/**
 * Description of Attributes
 *
 * @author Ivan Gospodinow <ivangospodinow@gmail.com>
 */
trait Attributes
{
    protected $attributes = [];

    /**
     *
     * @return []
     */
    public function getAttributes() : array
    {
        return $this->attributes;
    }
    
    /**
     *
     * @return []
     */
    public function setAttributes(array $attributes)
    {
        $this->attributes = $attributes;
    }
    
    /**
     *
     * @param string $name
     * @param string $value
     * @return \self
     */
    public function addAttribute(string $name, string $value)
    {
        if (array_key_exists($name, $this->attributes)) {
            $this->attributes[$name] .= $value;
        } else {
            $this->attributes[$name] = $value;
        }
    }

    /**
     *
     * @param string $name
     * @param string $value
     */
    public function setAttribute(string $name, string $value)
    {
        $this->attributes[$name] = $value;
    }

    /**
     *
     * @param string $name
     */
    public function removeAttribute(string $name)
    {
        unset($this->attributes[$name]);
    }

    /**
     *
     * @param string $name
     * @return type
     */
    public function getAttribute(string $name) : string
    {
        return $this->attributes[$name] ?? '';
    }

    /**
     *
     * @param type $addSplace
     * @return string
     */
    public function getAttributesString($addSplace = false) : string
    {
        return $this->createAttributesString($this->attributes, $addSplace);
    }

    /**
     *
     * @param type $attributes
     * @param type $addSplace
     * @return string
     */
    public function createAttributesString($attributes, $addSplace = false) : string
    {
        $str = [];
        foreach ($attributes as $name => $val) {
            $str[] = $name . '="' . $val . '"';
        }
        return ($addSplace && !empty($str) ? ' ' : '') . implode(' ', $str);
    }
}