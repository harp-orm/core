<?php

namespace Harp\Core\Model;

/**
 * Useful to get all public properties of an object
 *
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
trait PropertiesAccessorTrait
{
    /**
     * @param object $object
     * @return array
     */
    public static function getPublicPropertiesOf($object)
    {
        return get_object_vars($object);
    }

    /**
     * @return array
     */
    public function getProperties()
    {
        return PropertiesAccessorTrait::getPublicPropertiesOf($this);
    }

    /**
     * @param array $values
     */
    public function setProperties(array $values)
    {
        foreach ($values as $name => $value) {
            $this->$name = $value;
        }

        return $this;
    }
}
