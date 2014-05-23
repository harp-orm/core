<?php

namespace CL\LunaCore\Test\Unit\Model;

use CL\LunaCore\Model\PropertiesAccessorTrait;
use CL\LunaCore\Test\AbstractTestCase;

/**
 * @coversDefaultClass CL\LunaCore\Model\PropertiesAccessorTrait
 */
class PropertiesAccessorTraitTest extends AbstractTestCase
{
    /**
     * @covers ::getProperties
     * @covers ::getPublicPropertiesOf
     */
    public function testGetProperties()
    {
        $object = new TestClassAccessor();

        $properties = $object->getProperties();
        $expected = [
            'public1' => 'test1',
            'public2' => 'test2',
        ];

        $this->assertEquals($expected, $properties);
    }

    /**
     * @covers ::setProperties
     */
    public function testSetProperties()
    {
        $object = new TestClassAccessor();

        $object->setProperties([
            'public1' => 'test1 change',
            'public2' => 'test2 change',
        ]);

        $this->assertEquals('test1 change', $object->public1);
        $this->assertEquals('test2 change', $object->public2);
    }
}
