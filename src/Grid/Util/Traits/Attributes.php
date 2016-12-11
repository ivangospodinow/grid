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
     * @return string
     */
    public function getAttributesString($addSplace = false) : string
    {
        $str = [];
        foreach ($this->attributes as $name => $val) {
            $str[] = $name . '="' . $val . '"';
        }
        return ($addSplace && !empty($str) ? ' ' : '') . implode(' ', $str);
    }
}