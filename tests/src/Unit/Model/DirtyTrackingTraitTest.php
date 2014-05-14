<?php

namespace CL\LunaCore\Test\Unit\Model;

use CL\LunaCore\Model\DirtyTrackingTrait;
use CL\LunaCore\Test\AbstractTestCase;

class DirtyTrackingTraitTest extends AbstractTestCase
{
    /**
     * @covers CL\LunaCore\Model\DirtyTrackingTrait::setOriginals
     * @covers CL\LunaCore\Model\DirtyTrackingTrait::getOriginals
     * @covers CL\LunaCore\Model\DirtyTrackingTrait::getOriginal
     */
    public function testOriginals()
    {
        $object = new TestClassDirty();

        $originals = $object->getOriginals();
        $original = $object->getOriginal('test2');

        $this->assertEmpty($originals);
        $this->assertNull($original);

        $expected = get_object_vars($object);

        $object->setOriginals($expected);

        $originals = $object->getOriginals();
        $original = $object->getOriginal('test2');

        $this->assertEquals($expected, $originals);
        $this->assertEquals($expected['test2'], $original);
    }

    /**
     * @covers CL\LunaCore\Model\DirtyTrackingTrait::hasChange
     */
    public function testHasChange()
    {
        $object = new TestClassDirty();
        $object->setOriginals((array) $object);

        $this->assertFalse($object->hasChange('test'));

        $object->test = 'new val';

        $this->assertFalse($object->hasChange('test2'));
        $this->assertTrue($object->hasChange('test'));

        $object->test = 'test1';

        $this->assertFalse($object->hasChange('test2'));
        $this->assertFalse($object->hasChange('test'));
    }

    /**
     * @covers CL\LunaCore\Model\DirtyTrackingTrait::getChange
     */
    public function testGetChange()
    {
        $object = new TestClassDirty();
        $object->setOriginals(get_object_vars($object));

        $this->assertNull($object->getChange('test'));

        $object->test = 'new val';

        $expected = ['test1', 'new val'];

        $this->assertNull($object->getChange('test2'));
        $this->assertEquals($expected, $object->getChange('test'));

        $object->test = 'test1';

        $this->assertNull($object->getChange('test2'));
        $this->assertNull($object->getChange('test'));
    }

    /**
     * @covers CL\LunaCore\Model\DirtyTrackingTrait::getChanges
     */
    public function testGetChanges()
    {
        $object = new TestClassDirty();

        $object->setOriginals(get_object_vars($object));

        $this->assertEmpty($object->getChanges());

        $object->test = 'new val';

        $expected = ['test' => 'new val'];

        $this->assertEquals($expected, $object->getChanges());

        $object->test2 = 'other val';

        $expected = [
            'test' => 'new val',
            'test2' => 'other val',
        ];

        $this->assertEquals($expected, $object->getChanges());

        $object->test = 'test1';
        $object->test2 = 'test2';

        $this->assertEmpty($object->getChanges());
    }

    /**
     * @covers CL\LunaCore\Model\DirtyTrackingTrait::isEmptyChanges
     * @covers CL\LunaCore\Model\DirtyTrackingTrait::isChanged
     */
    public function testChanged()
    {
        $object = new TestClassDirty();

        $object->setOriginals(get_object_vars($object));

        $this->assertTrue($object->isEmptyChanges());
        $this->assertFalse($object->isChanged());

        $object->test = 'new val';

        $this->assertFalse($object->isEmptyChanges());
        $this->assertTrue($object->isChanged());

        $object->test2 = 'other val';

        $this->assertFalse($object->isEmptyChanges());
        $this->assertTrue($object->isChanged());

        $object->test = 'test1';
        $object->test2 = 'test2';

        $this->assertTrue($object->isEmptyChanges());
        $this->assertFalse($object->isChanged());
    }
}
