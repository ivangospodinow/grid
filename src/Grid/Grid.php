<?php

namespace Grid;

use Grid\Column\Column;

use Grid\Source\SourceInterface;
use Grid\Renderer\RendererInterface;
use Grid\Column\AbstractColumn;

use Grid\Interfaces\TranslateInterface;
use Grid\Interfaces\JavascriptCaptureInterface;

use Grid\Plugin\AutoloaderPlugin;
use Grid\Plugin\AbstractPlugin;
use Grid\Plugin\HeaderPlugin;
use Grid\Plugin\Interfaces\DataPluginInterface;
use Grid\Plugin\Interfaces\ColumnsPluginInterface;
use Grid\Plugin\ExtractorPlugin;
use Grid\Plugin\Interfaces\RowPluginInterface;
use Grid\Plugin\ProfilePlugin;
use Grid\Source\AbstractSource;
use Grid\Plugin\Interfaces\SourcePluginInterface;
use Grid\Plugin\Interfaces\HidratorPluginInterface;
use Grid\Plugin\Interfaces\RenderPluginInterface;
use Grid\Plugin\Interfaces\ColumnPluginInterface;
use Grid\Plugin\Interfaces\ColumnsPrePluginInterface;
use Grid\Plugin\Interfaces\DataPrePluginInterface;
use Grid\Plugin\Interfaces\ObjectDiPluginInterface;
use Grid\Plugin\Interfaces\JavascriptPluginInterface;

use Grid\Util\Traits\Attributes;
use Grid\Util\Traits\ExchangeArray;
use Grid\Util\Traits\Cache;

use \ArrayAccess;
use \Exception;

/**
 * Description of Grid
 *
 * @author Gospodinow
 */
class Grid implements ArrayAccess
{
    use Attributes, ExchangeArray, Cache;
    
    /**
     * All dependency objects
     * @var type
     */
    protected $iterator = [];

    /**
     *
     * @var SourceInterface
     */
    protected $source;
    
    /**
     *
     * @param array $config
     */
    public function __construct(array $config = [])
    {
        $this->setAttribute('id', $config['id'] ?? 'grid-id');
        $this->exchangeArray($config);
        $this[] = new AutoloaderPlugin;
    }
    
    /**
     *
     * @return string
     */
    public function getId() : string
    {
        return $this->getAttribute('id');
    }

    /**
     *
     * @return string
     */
    public function render() : string
    {
        $html = [];
        $html[] = $this->filter(RenderPluginInterface::class, 'preRender', '');
        foreach ($this[RendererInterface::class] as $renderer) {
            $html[] = $renderer->render($this);
        }
        return $this->filter(
            RenderPluginInterface::class,
            'postRender',
            implode(PHP_EOL, $html)
        );
    }

    /**
     * Noting is working without columns
     * Use preColumns for init config
     * @return array
     */
    public function getColumns() : array
    {
        if (!$this->hasCache(__METHOD__)) {
            $columns =  $this->filter(
                ColumnsPrePluginInterface::class,
                'preColumns',
                []
            );
            foreach ($this[AbstractColumn::class] as $column) {
                $columns[] = $this->filter(
                    ColumnPluginInterface::class,
                    'filterColumn',
                    $column
                );
            }
            $this->setCache(
                __METHOD__,
                $this->filter(
                    ColumnsPluginInterface::class,
                    'filterColumns',
                    $columns
                )
            );
        }
        return $this->getCache(__METHOD__);
    }

    /**
     *
     * @param string $name
     * @return AbstractColumn
     * @throws Exception
     */
    public function getColumn(string $name) : AbstractColumn
    {
        $key = __METHOD__ . '::' . $name;
        if (!$this->hasCache($key)) {
            $this->setCache($key, false);
            foreach ($this[AbstractColumn::class] as $column) {
                if ($column->getName() === $name) {
                    $this->setCache($key, $column);
                    break;
                }
            }
        }

        if ($this->getCache($key) === false) {
            throw new Exception('Column does not exists ' . $name);
        }

        return $this->getCache($key);
    }

    /**
     * Grid rows
     * @return array
     */
    public function getData() : array
    {
        if (!$this->hasCache(__METHOD__)) {
            $data = $this->filter(DataPrePluginInterface::class, 'preFilterData', []);
            
            $plugins   = $this[RowPluginInterface::class];
            $hydrators = $this[HidratorPluginInterface::class];
            
            foreach ($this[SourceInterface::class] as $source) {
                $this->filter(
                    SourcePluginInterface::class,
                    'filterSource',
                    $source
                );

                foreach ($source->getRows() as $rowData) {
                    foreach ($hydrators as $hydrator) {
                        $rowData = $hydrator->hydrate($rowData);
                    }
                    $row = $this->setObjectDi(new GridRow($rowData, GridRow::POSITION_BODY));
                    
                    foreach ($plugins as $plugin) {
                        $row = $plugin->filterRow($row);
                    }
                    $data[] = $row;
                }
            }
            
            $this->setCache(
                __METHOD__,
                $this->filter(
                    DataPluginInterface::class,
                    'filterData',
                    $data
                )
            );
        }
        return $this->getCache(__METHOD__);
    }

    /**
     *
     * @return int
     */
    public function getCount() : int
    {
        if (!$this->hasCache(__METHOD__)) {
            $count = 0;
            foreach  ($this[SourceInterface::class] as $source) {
                $count += $source->getCount();
            }
            $this->setCache(__METHOD__, (int) $count);
        }
        return $this->getCache(__METHOD__);
    }
    
    /**
     *
     * @param string $string
     */
    public function translate(string $string) : string
    {
        foreach ($this[TranslateInterface::class] as $translatable) {
            $string = $translatable->translate($string);
            if ($string !== $string) {
                break;
            }
        }
        return $string;
    }

    /**
     * Populates object dependency
     *
     * @param type $object
     */
    public function setObjectDi($object)
    {
        if ($object instanceof GridInterface) {
            $object->setGrid($this);
        }

        $this->filter(
            ObjectDiPluginInterface::class,
            'setObjectDi',
            $object
        );

        return $object;
    }

    /**
     * Calls plugins foreach
     * @param string $interface
     * @param string $method
     * @param type $data
     * @return type
     */
    public function filter(string $interface, string $method, $data = null)
    {
        foreach ($this[$interface] as $plugin) {
            $data = $plugin->$method($data);
        }
        return $data;
    }

    /**
     *
     * @param type $interface
     * @return bool
     */
    function offsetExists($interface): bool
    {
        return !empty($this->offsetGet($interface));
    }

    /**
     *
     * @param string $interface
     * @return type
     */
    public function offsetGet($interface)
    {
        $objects = [];
        foreach ($this->iterator as $mixed) {
            if ($mixed instanceof $interface) {
                $objects[] = $mixed;
            }
        }
        return $objects;
    }

    /**
     *
     * @param type $offset
     * @param type $value
     */
    public function offsetSet($offset, $value)
    {
        if (!is_object($value)) {
            throw new Exception('Offset must be object');
        }

        $this->setObjectDi($value);
        if (null !== $offset) {
            $this->iterator[$offset] = $value;
        } else {
            $this->iterator[] = $value;
        }
    }

    /**
     *
     * @param type $offset
     */
    public function offsetUnset($offset)
    {
        foreach ($this->iterator as $key => $mixed) {
            if (is_object($mixed)
            && $mixed instanceof $offset) {
                unset($this->iterator[$key]);
            }
        }
        unset($this->iterator[$offset]);
    }
}
