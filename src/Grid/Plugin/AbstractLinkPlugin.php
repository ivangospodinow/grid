<?php

namespace Grid\Plugin;

use Grid\Util\Extractor\AbstractExtractor;

use Grid\Util\Traits\Attributes;
use Grid\Util\Traits\ExchangeArray;
use Grid\Util\Traits\Required;

abstract class AbstractLinkPlugin extends AbstractPlugin
{
    use Attributes, ExchangeArray, Required;
    
    /**
     *
     * @param type $source
     * @param string $uri
     * @param array $uriParameters
     * @param string $label
     * @param array $attributes
     * @return type
     */
    public function createLink(
        $source,
        string $uri,
        array $uriParameters,
        string $label,
        array $attributes
    )
    {
        if (!empty($uriParameters)) {
            $extractor = AbstractExtractor::factory($source);
            $replace = [];
            foreach ($uriParameters as $key => $callback) {
                $replace[0][] = ':' . $key;
                $replace[1][] = $extractor->extract($source, $callback);
            }
            $uri = str_replace($replace[0], $replace[1], $uri);
        }

        $attributes['href'] = $uri;

        return sprintf(
            '<a%s>%s</a>',
            $this->createAttributesString($attributes, true),
            $label
        );
    }
}
