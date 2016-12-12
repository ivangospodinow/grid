<?php
namespace Grid\Column;

use Grid\Util\Traits\ExchangeArray;
use Grid\Util\Extractor\AbstractExtractor;
use Grid\Util\Traits\Attributes;

use \Exception;

/**
 * Every column must extend this class
 * More features coming
 *
 * @author Gospodinow
 */
abstract class AbstractColumn
{
    use ExchangeArray, Attributes;
    
    protected $name;
    protected $label;
    protected $extract;
    
    /**
     *
     * @var AbstractExtractor
     */
    protected $extractor;
    /**
     *
     * @param string $name
     * @param type $label
     * @param type $extract
     */
    public function __construct(string $name, $label, $extract = null)
    {
        $this->exchangeArray(
            [
                'name'      => $name,
                'label'     => $label,
                'extract'   => $extract ?? $name
            ]
        );
    }

    /**
     *
     * @param array $config
     * @return \self
     * @throws Exception
     */
    public static function factory(array $config) : self
    {
        if (!isset($config['name'])) {
            throw new Exception('name is required');
        }
        if (!isset($config['label'])) {
            throw new Exception('label is required');
        }

        return new Column(
            $config['name'],
            $config['label'],
            $config['extract'] ?? null
        );
    }

    /**
     *
     * @return string
     */
    public function getName() : string
    {
        return $this->name;
    }

    /**
     *
     * @return type
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     *
     * @return type
     */
    public function getExtract()
    {
        return $this->extract;
    }

    /**
     *
     * @return AbstractExtractor
     */
    public function getExtractor($source = null) : AbstractExtractor
    {
        if (null === $this->extractor) {
            $this->setExtractor(AbstractExtractor::factory($source));
        }
        return $this->extractor;
    }

    /**
     *
     * @param AbstractExtractor $extractor
     * @return \self
     */
    public function setExtractor(AbstractExtractor $extractor) : self
    {
        $this->extractor = $extractor;
        return $this;
    }
}
