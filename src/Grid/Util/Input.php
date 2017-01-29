<?php

namespace Grid\Util;

use Grid\Util\Traits\Attributes;
use Grid\Util\Traits\Required;

use \Exception;

/**
 * @TODO going to make separate objects for each type
 * but for now I need them to be an object
 * @author Ivan Gospodinow <ivangospodinow@gmail.com>
 */
class Input
{
    use Attributes, Required;

    const TYPE_TEXT   = 'text';
    const TYPE_SELECT = 'select';
    const TYPE_BUTTON = 'button';
    
    protected $valueOptions = [];
    
    public function __construct(array $config)
    {
        $this->required('name', $config, $this);

        if (!isset($config['type'])) {
            $config['type'] = self::TYPE_TEXT;
        }

        $this->setAttribute('name', $config['name']);
        $this->setAttribute('type', $config['type']);
        $this->setAttribute('value', $config['value'] ?? '');
        $this->setAttribute('placeholder', $config['placeholder'] ?? '');
        
        if (!in_array($config['type'], [self::TYPE_TEXT, self::TYPE_SELECT, self::TYPE_BUTTON])) {
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
        $html[] = $this->openTag();
        $html[] = $this->closeTag();
        return implode(PHP_EOL, $html);
    }

    /**
     *
     * @param array $values
     */
    public function setValueOptions(array $values)
    {
        $this->valueOptions = $values;
    }

    /**
     *
     * @return string
     */
    protected function openTag() : string
    {
        $html = '';
        if ($this->getAttribute('type') === self::TYPE_TEXT) {
            $html = sprintf(
                '<input%s',
                $this->getAttributesString(true)
            );
        } elseif ($this->getAttribute('type') === self::TYPE_SELECT) {
            if (!$this->getAttribute('onchange')) {
                $this->setAttribute('onchange', 'this.form.submit()');
            }
            $options = [];
            $options[] = '<option>' . $this->getAttribute('placeholder') . '</option>';
            foreach ($this->valueOptions as $value => $label) {
                $options[] = sprintf(
                    '<option value="%s"%s>%s</option>',
                    $value,
                    $value == $this->getValue() ? ' selected' : '',
                    $label
                );
            }
            $html = sprintf(
                '<select%s>%s',
                $this->getAttributesString(true),
                implode(PHP_EOL, $options)
            );
        } else if ($this->getAttribute('type') === self::TYPE_BUTTON) {
            $html = sprintf(
                '<button%s>$s',
                $this->getAttributesString(true),
                $this->getAttribute('placeholder') ?? $this->getValue()
            );
        }
        return $html;
    }

    public function closeTag() : string
    {
        $html = '';
        if ($this->getAttribute('type') === self::TYPE_TEXT) {
            $html = '/>';
        } elseif ($this->getAttribute('type') === self::TYPE_SELECT) {
            $html = '</select>';
        } elseif ($this->getAttribute('type') === self::TYPE_BUTTON) {
            $html = '</button>';
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