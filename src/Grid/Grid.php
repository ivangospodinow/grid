<?php

namespace Grid;

use Grid\Column\Column;

use Grid\Source\SourceInterface;
use Grid\Renderer\RendererInterface;
use Grid\Column\AbstractColumn;

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

use Grid\Util\Traits\Attributes;
use Grid\Util\Traits\ExchangeArray;

use \ArrayAccess;

/**
 * Description of Grid
 *
 * @author Gospodinow
 */
class Grid implements ArrayAccess
{
    use Attributes, ExchangeArray;

    /**
     * Unique grid Id
     * @var type
     */
    protected $id;

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
     * Object cache
     * @var type
     */
    protected $cache = [];

    /**
     *
     * @param array $config
     */
    public function __construct(array $config = [])
    {
        $config['id'] ?? $config['id'] = 'grid-id';
        $this->setAttribute('id', $config['id']);
        $this->exchangeArray($config);
        
        $this[] = new ExtractorPlugin;
        $this[] = new HeaderPlugin;
    }

    public static function factory(array $config) : self
    {
        $grid = new self($config);

        if (isset($config['source'])) {
            foreach ($config['source'] as $source) {
                $this[] = AbstractSource::factory($source);
            }
        }

        if (isset($config['columns'])) {
            foreach ($config['columns'] as $column) {
                $grid[] = Column::factory($column);
            }
        }

        if (isset($config['profile'])) {
            $grid[] = ProfilePlugin::factory($config['profile']);
        }
        
        if (isset($config['renderer']) && class_exists($config['renderer'])) {
            $grid[] = new $config['renderer'];
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
        $renderers = $this->getObjects(RendererInterface::class);
        $html = [];
        foreach ($renderers as $renderer) {
            $html[] = $renderer->render($this);
        }
        return implode(PHP_EOL, $html);
    }

    /**
     *
     * @return array
     */
    public function getColumns() : array
    {
        if (!array_key_exists(__METHOD__, $this->cache)) {
            $columns = $this->getObjects(AbstractColumn::class);
            $this->cache[__METHOD__] = $this->plugins(
                ColumnsPluginInterface::class,
                'filterColumns',
                $columns
            );
        }
        return $this->cache[__METHOD__];
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
     * Calls plugins foreach
     * @param type $interface
     * @param type $method
     * @param type $data
     * @return type
     */
    protected function plugins($interface, $method, $data)
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