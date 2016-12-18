<?php

namespace Grid\Util;

use Grid\Util\Traits\Attributes;

use \Exception;

/**
 *
 * @author Gospodinow
 */
class Input
{
    use Attributes;

    const TYPE_TEXT   = 'text';
    const TYPE_SELECT = 'select';

    public function __construct(string $name, $type = self::TYPE_TEXT)
    {
        $this->setAttribute('name', $name);
        $this->setAttribute('type', $type);

        if (!in_array($type, [self::TYPE_TEXT, self::TYPE_SELECT])) {
            throw new Exception('Unsupported type ' . $type);
        }
    }

    public function getName() : string
    {
        return $this->getAttribute('name');
    }

    /**
     *
     * @param string $value
     */
    public function setValue(string $value)
    {
        $this->setAttribute('value', $value);
    }

    /**
     *
     * @return type
     */
    public function getValue() : string
    {
        return $this->getAttribute('value');
    }

    public function render() : string
    {
        $html = [];
        $html[] = $this->openTab();
        $html[] = $this->closeTag();
        return implode(PHP_EOL, $html);
    }

    /**
     *
     * @return string
     */
    protected function openTab() : string
    {
        if ($this->getAttribute('type') === self::TYPE_TEXT) {
            return sprintf(
                '<input%s',
                $this->getAttributesString(true)
            );
        }
    }

    public function closeTag() : string
    {
        if ($this->getAttribute('type') === self::TYPE_TEXT) {
            return '/>';
        }
    }

    /**
     *
     * @param type $params
     * @return array
     */
    public static function createHiddenFromParams($params) : array
    {
        if (empty($params)) {
            return $params;
        }

        $pairs = explode('&', urldecode(http_build_query($params)));
        $inputs = [];
        foreach ($pairs as $pair) {
            $pair = explode('=', $pair);
            $inputs[$pair[0]] = sprintf(
                '<input name="%s" value="%s" type="hidden"/>',
                $pair[0],
                $pair[1]
            );
        }
        return $inputs;
    }
}