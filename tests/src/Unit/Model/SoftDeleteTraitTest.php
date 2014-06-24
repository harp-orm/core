<?php

namespace Harp\Core\Test\Unit\Model;

use Harp\Core\Model\SoftDeleteTrait;
use Harp\Core\Model\State;
use Harp\Core\Test\AbstractTestCase;

/**
 * @coversDefaultClass Harp\Core\Model\SoftDeleteTrait
 *
 * @author     Ivan Kerin <ikerin@gmail.com>
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
class SoftDeleteTraitTest extends AbstractTestCase
{
    /**
     * @covers ::delete
     */
    public function testDelete()
    {
        $object = new SoftDeleteModel(null, State::SAVED);

        $this->assertNull($object->deletedAt);

        $object->delete();

        $this->assertNotNull($object->deletedAt);
        $this->assertEquals(State::DELETED, $object->getState());
    }

    /**
     * @covers ::getDefaultState
     */
    public function testGetDefaultState()
    {
        $object = new SoftDeleteModel();

        $this->assertEquals(State::PENDING, $object->getDefaultState());

        $object->deletedAt = time();

        $this->assertEquals(State::DELETED, $object->getDefaultState());
    }

    /**
     * @covers ::restore
     * @covers ::isSoftDeleted
     */
    public function testRestore()
    {
        $object = new SoftDeleteModel(null, State::SAVED);

        $object->delete();

        $this->assertTrue($object->isDeleted());
        $this->assertTrue($object->isSoftDeleted());

        $object->restore();

        $this->assertTrue($object->isSaved());
        $this->assertFalse($object->isSoftDeleted());
    }

    /**
     * @covers ::realDelete
     * @covers ::isSoftDeleted
     */
    public function testRealDelete()
    {
        $object = new SoftDeleteModel(null, State::SAVED);

        $object->delete();

        $this->assertTrue($object->isDeleted());
        $this->assertTrue($object->isSoftDeleted());

        $object->realDelete();

        $this->assertTrue($object->isDeleted());
        $this->assertFalse($object->isSoftDeleted());
    }
}
