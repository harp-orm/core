<?php

namespace Harp\Core\Test\Unit\Model;

use Harp\Core\Model\DirtyTrackingTrait;
use Harp\Core\Test\AbstractTestCase;

/**
 * @coversDefaultClass Harp\Core\Model\DirtyTrackingTrait
 *
 * @author     Ivan Kerin <ikerin@gmail.com>
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://spdx.org/licenses/BSD-3-Clause
 */
class DirtyTrackingTraitTest extends AbstractTestCase
{
    /**
     * @covers ::setOriginals
     * @covers ::getOriginals
     * @covers ::getOriginal
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
     * @covers ::hasChange
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
     * @covers ::getChange
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
     * @covers ::getChanges
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
     * @covers ::isEmptyChanges
     * @covers ::isChanged
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
