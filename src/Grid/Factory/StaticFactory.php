<?php

namespace Grid\Factory;

use Grid\Grid;

use \Exception;

/**
 * @author Ivan Gospodinow <ivangospodinow@gmail.com>
 */
class StaticFactory
{
    /**
     * Creates all the plugins and grid from config array
     *
     * @param array $configs
     * @return \self
     * @throws Exception
     */
    public static function factory(array $configs) : Grid
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
}
