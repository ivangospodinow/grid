<?php
namespace Grid\Source\Traits;

/**
 *
 * @author Gospodinow
 */
trait NamespaceAwareTrait
{
    /**
     *
     * @var string
     */
    protected $namespace;
    
    /**
     * users.name
     * @param type $field
     * @return string
     */
    protected function getDbFieldNamespace(string $field) : string
    {
        /**
         * If field has namespace in it, return it
         */
        if (strpos($field, '.') !== false) {
            return $field;
        }
        return ($this->namespace ? $this->namespace . '.' : '') . $field;
    }
}
