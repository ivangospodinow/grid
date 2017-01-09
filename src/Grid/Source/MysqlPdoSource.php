<?php
namespace Grid\Source;

use Zend\Db\Adapter\Driver\Pdo\Pdo as ZendPdo;
use Zend\Db\Adapter\Adapter;

use \PDO;
use \Exception;

/**
 * Zf pdo driver
 *
 * @author Ivan Gospodinow <ivangospodinow@gmail.com>
 */
class MysqlPdoSource extends ZendDbAdapterSource
{
    /**
     *
     * @param array $config
     * @throws Exception
     */
    public function __construct(array $config)
    {
        if (!isset($config['driver'])
        || !$config['driver'] instanceof PDO) {
            throw new Exception('driver must be instance of PDO');
        }
        $config['driver'] = new Adapter(new ZendPdo($config['driver']));

        parent::__construct($config);
    }
}