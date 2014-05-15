<?php

namespace CL\LunaCore\Model;

/**
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
trait PropertiesAccessorTrait
{
    /**
     * @param PropertiesAccessorTrait $object
     */
    public static function getPublicPropertiesOf($object)
    {
        return get_object_vars($object);
    }

    public function getProperties()
    {
        return PropertiesAccessorTrait::getPublicPropertiesOf($this);
    }

    public function setProperties(array $values)
    {
        foreach ($values as $name => $value) {
            $this->$name = $value;
        }

        return $this;
    }
}
