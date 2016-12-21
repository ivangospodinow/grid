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

    public function __construct(array $config)
    {
        if (!isset($config['name'])) {
            throw new Exception('parameter name is required');
        }

        if (!isset($config['type'])) {
            $config['type'] = self::TYPE_TEXT;
        }

        $this->setAttribute('name', $config['name']);
        $this->setAttribute('type', $config['type']);
        $this->setAttribute('value', $config['value'] ?? '');

        if (!in_array($config['type'], [self::TYPE_TEXT, self::TYPE_SELECT])) {
            throw new Exception('Unsupported type ' . $config['type']);
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
        $html = '';
        if ($this->getAttribute('type') === self::TYPE_TEXT) {
            $html = sprintf(
                '<input%s',
                $this->getAttributesString(true)
            );
        }
        return $html;
    }

    public function closeTag() : string
    {
        $html = '';
        if ($this->getAttribute('type') === self::TYPE_TEXT) {
            $html = '/>';
        }
        return $html;
    }

    /**
     *
     * @param type $params
     * @return array
     */
    public static function createHiddenFromParams(array $params) : array
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