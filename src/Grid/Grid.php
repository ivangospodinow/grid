<?php

namespace Grid;

use Grid\Column\Column;

use Grid\Source\SourceInterface;
use Grid\Renderer\RendererInterface;
use Grid\Column\AbstractColumn;

use Grid\Interfaces\TranslateInterface;

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

use Grid\Plugin\DataTypesPlugin;

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
     * Unique grid Id
     * @var type
     */
    protected $id;

    /**
     * Autoloading default plugins
     * By disabling it, you can optimize for performance
     * 
     * @var type
     */
    protected $autoload = true;

    /**
     * marks if there is at least one column data type
     * @var bool
     */
    protected $columnsHasDataType = false;

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
        $config['id'] ?? $config['id'] = 'grid-id';
        $this->setAttribute('id', $config['id']);
        $this->exchangeArray($config);

        if ($this->canAutoload()) {
            $this[] = new ExtractorPlugin;
            $this[] = new HeaderPlugin;
        }
    }

    /**
     * Creates all the plugins and grid from config array
     * 
     * @param array $configs
     * @return \self
     * @throws Exception
     */
    public static function factory(array $configs) : self
    {
        $grid = null;
        $plugins = [];
        foreach ($configs as $config) {
            
            $plugin = null;
            if (is_string($config)) {
                $plugins[] = new $config;
                continue;
            }

            if (!isset($config['options'])) {
                $config['options'] = [];
            }

            /**
             * Create from callback factory
             */
            if (isset($config['callback'])) {
                if (!isset($config['callback'][0])
                || !isset($config['callback'][1])) {
                    throw new Exception('callback must have 0=>object,class 1=> method');
                }

                if (is_string($config['callback'][0])) {
                    $config['callback'][0] = new $config['callback'][0];
                }

                $plugin = call_user_func_array(
                    $config['callback'],
                    [$config['options']]
                );
            } elseif (isset($config['class'])) {
                $plugin = new $config['class']($config['options']);
            } else {
                throw new Exception('Plugin factory required callback or class');
            }
            
            if (!is_object($plugin)) {
                throw new Exception('At this point plugin most be object');
            }

            if ($plugin instanceof self) {
                $grid = $plugin;
            } else {
                $plugins[] = $plugin;
            }
        }

        if (!$grid instanceof self) {
            $grid = new Grid($configs);
        }

        foreach ($plugins as $plugin) {
            $grid[] = $plugin;
        }

        return $grid;
    }

    /**
     *
     * @return string
     */
    public function getId() : string
    {
        return (string) $this->getAttribute('id');
    }

    /**
     *
     * @return string
     */
    public function render() : string
    {
        $this->plugins(RenderPluginInterface::class, 'preRender');
        
        $renderers = $this->getObjects(RendererInterface::class);
        $html = [];
        foreach ($renderers as $renderer) {
            $html[] = $renderer->render($this);
        }

        return $this->plugins(
            RenderPluginInterface::class,
            'postRender',
            implode(PHP_EOL, $html)
        );
    }

    /**
     *
     * @return array
     */
    public function getColumns() : array
    {
        if (!array_key_exists(__METHOD__, $this->cache)) {
            $columns = [];
            foreach ($this->iterator as $mixed) {
                if (is_object($mixed)
                && $mixed instanceof AbstractColumn) {
                    
                    if (!$this->columnsHasDataType
                    && $mixed->hasDataType()) {
                        $this->columnsHasDataType = true;
                    }
                    
                    $columns[] = $this->plugins(
                        ColumnPluginInterface::class,
                        'filterColumn',
                        $mixed
                    );
                }
            }
            $this->cache[__METHOD__] = $this->plugins(
                ColumnsPluginInterface::class,
                'filterColumns',
                $columns
            );

            /**
             * Autoload data types
             */
            if ($this->autoload
            && $this->columnsHasDataType
            && !count($this->getObjects(DataTypesPlugin::class))) {
                $this[] = new DataTypesPlugin;
            }
        }
        return $this->cache[__METHOD__];
    }

    /**
     *
     * @param string $name
     * @return AbstractColumn
     */
    public function getColumn(string $name) : AbstractColumn
    {
        $key = __METHOD__ . '::' . $name;
        if (!isset($this->cache[$key])) {
            foreach ($this->getObjects(AbstractColumn::class) as $column) {
                if ($column->getName() === $name) {
                    $this->cache[$key] = $column;
                    break;
                }
            }
        }
        return $this->cache[$key];
    }

    /**
     * Grid rows
     * @return array
     */
    public function getData() : array
    {
        if (!array_key_exists(__METHOD__, $this->cache)) {
            $sources   = $this->getObjects(SourceInterface::class);
            $plugins   = $this->getObjects(RowPluginInterface::class);
            $hydrators = $this->getObjects(HidratorPluginInterface::class);
            
            $data = [];
            foreach ($sources as $source) {
                $this->plugins(
                    SourcePluginInterface::class,
                    'filterSource',
                    $source
                );

                if ($source->canOrder()) {
                    $source->order();
                }

                foreach ($source->getRows() as $rowData) {

                    foreach ($hydrators as $hydrator) {
                        $rowData = $hydrator->hydrate($rowData);
                    }

                    $row = new GridRow($rowData, $this);
                    foreach ($plugins as $plugin) {
                        $row = $plugin->filterRow($row);
                    }
                    $data[] = $row;
                }
            }
            unset($plugins);
            $this->cache[__METHOD__] = $this->plugins(
                DataPluginInterface::class,
                'filterData',
                $data
            );
        }
        return $this->cache[__METHOD__];
    }

    /**
     *
     * @return int
     */
    public function getCount() : int
    {
        if (!array_key_exists(__METHOD__, $this->cache)) {
            $count = 0;
            $sources = $this->getObjects(SourceInterface::class);
            foreach  ($sources as $source) {
                $count += $source->getCount();
            }
            $this->cache[__METHOD__] = (int) $count;
        }
        return $this->cache[__METHOD__];
    }

    /**
     *
     * @param type $interface
     * @return array
     */
    public function getObjects($interface) : array
    {
        $objects = [];
        foreach ($this->iterator as $mixed) {
            if (is_object($mixed)
            && $mixed instanceof $interface) {
                $objects[] = $mixed;
            }
        }
        return $objects;
    }

    /**
     *
     * @param string $string
     */
    public function translate(string $string) : string
    {
        if (!isset($this->cache[__METHOD__])) {
            $this->cache[__METHOD__] = $this->getObjects(TranslateInterface::class);
        }

        foreach ($this->cache[__METHOD__] as $translatable) {
            $string = $translatable->translate($string);
            if ($string !== $string) {
                break;
            }
        }
        return $string;
    }

    /**
     *
     * @return bool
     */
    public function canAutoload() : bool
    {
        return $this->autoload;
    }

    /**
     * Calls plugins foreach
     * @param type $interface
     * @param type $method
     * @param type $data
     * @return type
     */
    protected function plugins($interface, $method, $data = null)
    {
        $plugins = $this->getObjects($interface);
        foreach ($plugins as $plugin) {
            $data = $plugin->$method($data);
        }
        return $data;
    }

    /**
     *
     * @param type $offset
     * @return bool
     */
    function offsetExists($offset): bool
    {
        return array_key_exists($offset, $this->iterator);
    }

    /**
     *
     * @param type $offset
     * @return type
     */
    public function offsetGet($offset)
    {
        return $this->iterator[$offset] ?? null;
    }

    /**
     *
     * @param type $offset
     * @param type $value
     */
    public function offsetSet($offset, $value)
    {
        if ($value instanceof GridInterface) {
            $value->setGrid($this);
        }
        if ($offset === null) {
            $this->iterator[] = $value;
        } else {
            $this->iterator[$offset] = $value;
        }
    }

    /**
     *
     * @param type $offset
     */
    public function offsetUnset($offset)
    {
        unset($this->iterator[$offset]);
    }
}