<?php

namespace CL\LunaCore\Test\Unit\Model;

use CL\LunaCore\Model\SoftDeleteTrait;
use CL\LunaCore\Model\State;
use CL\LunaCore\Test\AbstractTestCase;

/**
 * @coversDefaultClass CL\LunaCore\Model\SoftDeleteTrait
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
