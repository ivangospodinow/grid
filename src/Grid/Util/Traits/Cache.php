<?php
namespace Grid\Util\Traits;

/**
 *
 * @author Gospodinow
 */
trait Cache
{
    /**
     * Object cache
     * @var type
     */
    protected $cache = [];

    public function getCache(string $key)
    {
        if ($this->hasCache($key)) {
            return $this->cache[$key];
        }
        return null;
    }

    /**
     *
     * @param string $key
     * @param type $value
     */
    public function setCache(string $key, $value)
    {
        $this->cache[$key] = $value;
    }

    /**
     *
     * @param string $key
     * @return type
     */
    public function hasCache(string $key)
    {
        return array_key_exists($key, $this->cache);
    }
}