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

        /**
         * Disable autoload to full control
         * and slim performance increase
         */
        if ($this->canAutoload()) {
            $this[] = new AutoloaderPlugin;
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

            if (is_array($config)
            && !isset($config['options'])) {
                $config['options'] = [];
            }

            $plugin = null;
            if (is_object($config)) {
                $plugin = $config;
            } elseif (is_string($config)) {
                if (class_exists($config)) {
                    $plugin = new $config;
                } elseif (is_string($config)) {
                    throw new Exception('String config expects class, given ' . $config);
                }
            } elseif (isset($config['callback'])) {
               if (!isset($config['callback'][0])
                || !isset($config['callback'][1])) {
                    throw new Exception('callback must have 0=>object,class 1=> method');
                }

                if (is_string($config['callback'][0])) {
                    if (!class_exists($config['callback'][0])) {
                        throw new Exception($config['callback'][0] . ' does not exists');
                    }
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
            
            if ($plugin instanceof Grid) {
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
        $html = [];
        $html[] = $this->filter(RenderPluginInterface::class, 'preRender', '');
        
        foreach ($this[RendererInterface::class] as $renderer) {
            $html[] = $renderer->render($this);
        }

        foreach ($this[JavascriptCaptureInterface::class] as $scriptCapture) {

            $script =
            $this->filter(
               JavascriptPluginInterface::class,
               'addJavascript',
               $scriptCapture
            );
            
            $html[] = sprintf(
                '<script>%s%s%s</script>',
                PHP_EOL,
                (string) $script,
                PHP_EOL
            );
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
        if (!array_key_exists(__METHOD__, $this->cache)) {
            $columns =  $this->filter(
                ColumnsPrePluginInterface::class,
                'preColumns',
                []
            );
            foreach ($this->iterator as $mixed) {
                if (is_object($mixed)
                && $mixed instanceof AbstractColumn) {
                    
                    $columns[] = $this->filter(
                        ColumnPluginInterface::class,
                        'filterColumn',
                        $mixed
                    );
                }
            }
            $this->cache[__METHOD__] = $this->filter(
                ColumnsPluginInterface::class,
                'filterColumns',
                $columns
            );
        }
        return $this->cache[__METHOD__];
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
        if (!isset($this->cache[$key])) {
            $this->cache[$key] = false;
            foreach ($this[AbstractColumn::class] as $column) {
                if ($column->getName() === $name) {
                    $this->cache[$key] = $column;
                    break;
                }
            }
        }

        if ($this->cache[$key] === false) {
            throw new Exception('Column does not exists ' . $name);
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
                    $row = new GridRow($rowData, $this, GridRow::POSITION_BODY);
                    
                    foreach ($plugins as $plugin) {
                        $row = $plugin->filterRow($row);
                    }
                    $data[] = $row;
                }
            }
            
            $this->cache[__METHOD__] = $this->filter(
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
            foreach  ($this[SourceInterface::class] as $source) {
                $count += $source->getCount();
            }
            $this->cache[__METHOD__] = (int) $count;
        }
        return $this->cache[__METHOD__];
    }
    
    /**
     *
     * @param string $string
     */
    public function translate(string $string) : string
    {
        if (!isset($this->cache[__METHOD__])) {
            $this->cache[__METHOD__] = $this[TranslateInterface::class];
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