<?php

namespace Grid\Factory;

use Grid\Grid;

use \Exception;

class ConfigFactory
{
    /**
     * Structure of config
     * [
     *   'grid' => [
     *     'plugins' => [GLOBAL PLUGINS],
     *      '$id' => [
     *        GRID CONFIG
     *        COLUMNS
              PLUGINS AND ETC.
     *        'profiles' => [
     *          '$profile' => [
     *            'columns' => [
     *              'id', 'name' ...
     *            ],
     *            'plugins' => [PROFILE SPECIFIC PLUGINS]
     *          ]
     *        ],
     *      ]
     *   ],
     * ]
     *
     * @param string $id
     * @param string $profile
     * @param array $config
     */
    public static function factory(string $id, string $profile, array $config) : Grid
    {
        $gridConfig = $config['grid'];
        if (!isset($gridConfig[$id]['profiles'][$profile])) {
            throw new Exception('Grid id or profile does not exists');
        }

        if (!isset($gridConfig['plugins'])) {
            $gridConfig['plugins'] = [];
        }

        if (!isset($gridConfig[$id]['plugins'])) {
            $gridConfig[$id]['plugins'] = [];
        }

        if (!isset($gridConfig[$id]['profiles'][$profile]['plugins'])) {
            $gridConfig[$id]['profiles'][$profile]['plugins'] = [];
        }

        $configs = array_merge(
            $gridConfig['plugins'],
            $gridConfig[$id]['plugins'],
            $gridConfig[$id]['profiles'][$profile]['plugins'],
            $gridConfig[$id]['columns']
        );
        
        $profileConfig = $gridConfig[$id]['profiles'][$profile];
        
        $configs[] = [
            'class' => \Grid\Plugin\ProfilePlugin::class,
            'options' => [
                'columns' => $profileConfig['columns']
            ]
        ];

        return StaticFactory::factory($configs);
    }
}
