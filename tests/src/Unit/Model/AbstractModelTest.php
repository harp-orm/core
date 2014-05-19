<?php

namespace CL\LunaCore\Test\Unit\Model;

use CL\LunaCore\Model\AbstractModel;
use CL\LunaCore\Model\State;
use CL\LunaCore\Repo\Event;
use CL\LunaCore\Test\AbstractTestCase;

class AbstractModelTest extends AbstractTestCase
{
   /**
     * @covers CL\LunaCore\Model\AbstractModel::__construct
     * @covers CL\LunaCore\Model\AbstractModel::getState
     */
    public function testConstrut()
    {
        $model = new Model();

        $this->assertEmpty($model->getUnmapped());
        $this->assertTrue($model->isPending());
        $this->assertEmpty($model->id);
        $this->assertEquals('test', $model->name);

        $model = new Model(['id' => 10, 'name' => 'name1', 'test' => 'testval'], State::SAVED);

        $this->assertEquals(['test' => 'testval'], $model->getUnmapped());
        $this->assertTrue($model->isSaved());
        $this->assertEquals(10, $model->id);
        $this->assertEquals('name1', $model->name);
        $this->assertEquals(['id' => 10, 'name' => 'name1', 'class' => Model::class], $model->getOriginals());
    }

    /**
     * @covers CL\LunaCore\Model\AbstractModel::resetOriginals
     */
    public function testResetOriginals()
    {
        $model = new Model(['id' => 1, 'name' => 'test 2']);

        $expected = ['id' => 1, 'name' => 'test 2', 'class' => Model::class];

        $model->name = 'test 3';
        $model->id = 4;

        $this->assertEquals($expected, $model->getOriginals());

        $model->resetOriginals();

        $expected = ['id' => 4, 'name' => 'test 3', 'class' => Model::class];

        $this->assertEquals($expected, $model->getOriginals());
    }

    public function dataSetStateNotVoid()
    {
        return [
            [
                ['id' => null],
                State::VOID,
                State::PENDING,
            ],
            [
                ['id' => 10],
                State::VOID,
                State::SAVED,
            ],
            [
                ['id' => null],
                State::SAVED,
                State::SAVED,
            ],
            [
                ['id' => 10],
                State::PENDING,
                State::PENDING,
            ],
        ];
    }

    /**
     * @dataProvider dataSetStateNotVoid
     * @covers CL\LunaCore\Model\AbstractModel::setStateNotVoid
     */
    public function testSetStateNotVoid($parameters, $state, $expected)
    {
        $model = new Model($parameters, $state);

        $model->setStateNotVoid();
        $this->assertEquals($expected, $model->getState());
    }

    /**
     * @covers CL\LunaCore\Model\AbstractModel::getDefaultState
     */
    public function testGetDefaultState()
    {
        $model = new Model();

        $model->setStateNotVoid();
        $this->assertEquals(State::PENDING, $model->getState());
    }


    /**
     * @covers CL\LunaCore\Model\AbstractModel::setStateVoid
     * @covers CL\LunaCore\Model\AbstractModel::isVoid
     */
    public function testStateVoid()
    {
        $model = new Model();

        $this->assertFalse($model->isVoid());

        $model->setStateVoid();

        $this->assertEquals(State::VOID, $model->getState());
        $this->assertTrue($model->isVoid());
    }

    /**
     * @covers CL\LunaCore\Model\AbstractModel::isPending
     * @covers CL\LunaCore\Model\AbstractModel::setState
     */
    public function testIsPending()
    {
        $model = new Model(null, State::VOID);

        $this->assertFalse($model->isPending());
        $model->setState(State::PENDING);
        $this->assertTrue($model->isPending());
    }


    /**
     * @covers CL\LunaCore\Model\AbstractModel::isSaved
     * @covers CL\LunaCore\Model\AbstractModel::setState
     */
    public function testIsSaved()
    {
        $model = new Model(null, State::VOID);

        $this->assertFalse($model->isSaved());
        $model->setState(State::SAVED);
        $this->assertTrue($model->isSaved());
    }

    /**
     * @covers CL\LunaCore\Model\AbstractModel::isDeleted
     * @covers CL\LunaCore\Model\AbstractModel::setState
     */
    public function testIsDeleted()
    {
        $model = new Model(null, State::VOID);

        $this->assertFalse($model->isDeleted());
        $model->setState(State::DELETED);
        $this->assertTrue($model->isDeleted());
    }

    /**
     * @covers CL\LunaCore\Model\AbstractModel::isSoftDeleted
     */
    public function testIsSoftDeleted()
    {
        $model = new Model();

        $this->assertFalse($model->isSoftDeleted());
    }

    /**
     * @covers CL\LunaCore\Model\AbstractModel::delete
     */
    public function testDelete()
    {
        $model = new Model(null, State::SAVED);

        $this->assertFalse($model->isDeleted());
        $model->delete();
        $this->assertTrue($model->isDeleted());

        $model = new Model(null, State::VOID);

        $this->assertFalse($model->isDeleted());
        $model->delete();
        $this->assertFalse($model->isDeleted(), 'Should not delete if void');
    }

    /**
     * @covers CL\LunaCore\Model\AbstractModel::delete
     * @expectedException LogicException
     */
    public function testDeletePending()
    {
        $model = new Model(null, State::PENDING);

        $model->delete();
    }

    /**
     * @covers CL\LunaCore\Model\AbstractModel::getId
     * @covers CL\LunaCore\Model\AbstractModel::setId
     */
    public function testGetId()
    {
        $model = new Model();

        $this->assertEquals(null, $model->getId());

        $model->id = 20;

        $this->assertEquals(20, $model->getId());

        $model->setId(30);

        $this->assertEquals(30, $model->id);
        $this->assertEquals(30, $model->getId());
    }

    /**
     * @covers CL\LunaCore\Model\AbstractModel::getErrors
     * @covers CL\LunaCore\Model\AbstractModel::isEmptyErrors
     * @covers CL\LunaCore\Model\AbstractModel::validate
     */
    public function testErrors()
    {
        $model = new Model();

        $this->assertInstanceOf('CL\Carpo\Errors', $model->getErrors());
        $this->assertCount(0, $model->getErrors());
        $this->assertTrue($model->isEmptyErrors());

        $model->name = null;
        $model->other = null;

        $result = $model->validate();
        $this->assertFalse($result);

        $this->assertInstanceOf('CL\Carpo\Errors', $model->getErrors());
        $this->assertCount(2, $model->getErrors());
        $this->assertFalse($model->isEmptyErrors());
        $this->assertCount(1, $model->getErrors()->onlyFor('name'));
        $this->assertCount(1, $model->getErrors()->onlyFor('other'));
    }
}
