<?php

namespace CL\LunaCore\Test\Unit\Model;

use CL\LunaCore\Model\UnmappedPropertiesTrait;
use CL\LunaCore\Test\AbstractTestCase;

class UnmappedPropertiesTraitTest extends AbstractTestCase
{
    /**
     * @covers CL\LunaCore\Model\UnmappedPropertiesTrait
     */
    public function testAll()
    {
        $object = new TestClassUnmapped();

        $this->assertEmpty($object->getUnmapped());
        $this->assertFalse(isset($object->test3));

        $object->test1 = 'val1';
        $object->test2 = 'val2';

        $this->assertEmpty($object->getUnmapped());
        $this->assertFalse(isset($object->test3));
        $this->assertFalse(isset($object->test4));

        $object->test3 = 'val3';
        $object->test4 = 'val4';

        $expected = [
            'test3' => 'val3',
            'test4' => 'val4',
        ];

        $this->assertEquals($expected, $object->getUnmapped());
        $this->assertEquals('val3', $object->test3);
        $this->assertEquals('val4', $object->test4);
        $this->assertTrue(isset($object->test4));
        $this->assertTrue(isset($object->test4));
    }
}
