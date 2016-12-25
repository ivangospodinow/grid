<?php
namespace Grid\Util\Traits;

/**
 * Description of Attributes
 *
 * @author Gospodinow
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
    public function addAttribute(string $name, string $value) : self
    {
        if (array_key_exists($name, $this->attributes)) {
            $this->attributes[$name] .= $value;
        } else {
            $this->attributes[$name] = $value;
        }
        return $this;
    }

    /**
     *
     * @param string $name
     * @param string $value
     * @return \self
     */
    public function setAttribute(string $name, string $value) : self
    {
        $this->attributes[$name] = $value;
        return $this;
    }

    /**
     *
     * @param string $name
     * @return type
     */
    public function getAttribute(string $name)
    {
        return $this->attributes[$name] ?? null;
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