<?php
namespace Grid\Column;

use Grid\Util\Extractor\AbstractExtractor;
use Grid\Util\Traits\ExchangeArray;
use Grid\Util\Traits\Attributes;

use Grid\Util\Traits\GridAwareTrait;
use Grid\GridInterface;

use \Exception;

/**
 * Every column must extend this class
 * More features coming
 *
 * @author Gospodinow
 */
abstract class AbstractColumn implements GridInterface
{
    use ExchangeArray, Attributes, GridAwareTrait;

    const SEARCHABLE_STRATEGY_EQ   = '=';
    const SEARCHABLE_STRATEGY_LIKE = 'like';

    /**
     * Unique identifier within the grid
     * @var string
     */
    protected $name;

    /**
     *
     * @var string
     */
    protected $preLabel = '';

    /**
     *
     * @var string
     */
    protected $label = '';

    /**
     *
     * @var string
     */
    protected $postLabel = '';
    
    /**
     *
     * @var type
     */
    protected $labelIsTranslated = false;

    /**
     * Gets column value from array or object
     * @var type
     */
    protected $extract;
    
    /**
     * Called based on extract and row value
     * @var AbstractExtractor
     */
    protected $extractor;
    
    /**
     * Corresponding field or fields for this column
     * For example User full name = user.name + user.lastName;
     * @example [user.name, user.lastName]
     * @var array
     */
    protected $dbFields;

    /**
     * If the column can be sortable
     * @var type 
     */
    protected $sortable = false;

    /**
     *
     * @var type
     */
    protected $searchable = false;

    /**
     *
     * @var string
     */
    protected $searchableStrategy = self::SEARCHABLE_STRATEGY_LIKE;
    
    /**
     *
     * @param string $name
     * @param type $label
     * @param type $extract
     */
    public function __construct(array $config)
    {
        if (!isset($config['name'])) {
            throw new Exception('Column required name');
        }
        $config['extract']  = $config['extract'] ?? $config['name'];
        $config['dbFields'] = isset($config['dbFields'])
                            && !is_array($config['dbFields'])
                            ? [$config['dbFields']] : [];

        $this->exchangeArray($config);
    }

    /**
     *
     * @param array $config
     * @return \self
     * @throws Exception
     */
    public static function factory(array $config) : self
    {
        return new Column($config);
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
        if (!$this->labelIsTranslated) {
            $this->labelIsTranslated = true;
            $this->label = $this->getGrid()->translate($this->label);
        }

        return $this->label;
    }

    /**
     *
     * @param string $label
     * @return \self
     */
    public function setLabel(string $label) : self
    {
        $this->label = $label;
        return $this;
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
     * @return string
     */
    public function getPreLabel() : string
    {
        return $this->preLabel;
    }

    /**
     *
     * @param string $string
     */
    public function setPreLabel(string $string)
    {
        $this->preLabel = $string;
    }

    /**
     *
     * @return string
     */
    public function getPostLabel() : string
    {
        return $this->postLabel;
    }

    /**
     *
     * @param string $string
     */
    public function setPostLabel(string $string)
    {
        $this->postLabel = $string;
    }

    /**
     *
     * @return array
     */
    public function getDbFields() : array
    {
        return $this->dbFields;
    }

    /**
     *
     * @return bool
     */
    public function hasDbFields() : bool
    {
        return !empty($this->dbFields);
    }

    /**
     *
     * @return bool
     */
    public function isSortable() : bool
    {
        return $this->hasDbFields() && $this->sortable;
    }

    /**
     *
     * @return bool
     */
    public function isSearchable() : bool
    {
        return $this->hasDbFields() && $this->searchable;
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
