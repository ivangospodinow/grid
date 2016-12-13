<?php
namespace Grid\Source;

use Zend\Db\Adapter\Driver\Mysqli\Mysqli as ZendMysqli;
use Zend\Db\Adapter\Adapter;

use \mysqli;
use \Exception;

/**
 * Zf pdo driver
 *
 * @author Gospodinow
 */
class MysqliSource extends ZendDbAdapterSource
{
    /**
     *
     * @param array $config
     * @throws Exception
     */
    public function __construct(array $config)
    {
        if (!isset($config['driver'])
        || !$config['driver'] instanceof mysqli) {
            throw new Exception('driver must be instance of mysqli');
        }
        $config['driver'] = new Adapter(new ZendMysqli($config['driver']));

        parent::__construct($config);
    }
}