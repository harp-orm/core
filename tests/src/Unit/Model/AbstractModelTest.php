<?php

namespace CL\LunaCore\Test\Unit\Model;

use CL\LunaCore\Model\AbstractModel;
use CL\LunaCore\Repo\ModelEvent;
use CL\LunaCore\Test\AbstractTestCase;
use CL\Carpo\Asserts;
use CL\Carpo\Assert\Present;

class AbstractModelTest extends AbstractTestCase
{
    public function dataConstruct()
    {
        return [
            [
                null,
                null,
                ['id' => null, 'name' => 'test'],
                AbstractModel::PENDING,
                null,
            ],
            [
                ['id' => 1, 'name' => 'test 2'],
                null,
                ['id' => 1, 'name' => 'test 2'],
                AbstractModel::PENDING,
                null,
            ],
            [
                ['id' => 1, 'name' => 'test 2', 'test' => 'test 3'],
                null,
                ['id' => 1, 'name' => 'test 2'],
                AbstractModel::PENDING,
                ['test' => 'test 3'],
            ],
            [
                ['id' => 1, 'name' => 'test 2', 'test' => 'test 3'],
                AbstractModel::PERSISTED,
                ['id' => 1, 'name' => 'test 2'],
                AbstractModel::PERSISTED,
                ['test' => 'test 3'],
            ],
        ];
    }

    /**
     * @dataProvider dataConstruct
     * @covers CL\LunaCore\Model\AbstractModel::__construct
     * @covers CL\LunaCore\Model\AbstractModel::getState
     */
    public function testConstrut($properties, $state, $expectedProperties, $expectedState, $expectedUnmapped)
    {
        if ($state) {
            $model = new Model($properties, $state);
        } elseif ($properties) {
            $model = new Model($properties);
        } else {
            $model = new Model();
        }

        $this->assertEquals($expectedProperties, $model->getProperties());
        $this->assertEquals($expectedProperties, $model->getOriginals());
        $this->assertEquals($expectedState, $model->getState());
        $this->assertEquals($expectedUnmapped, $model->getUnmapped());
    }

    /**
     * @covers CL\LunaCore\Model\AbstractModel::resetOriginals
     */
    public function testResetOriginals()
    {
        $model = new Model(['id' => 1, 'name' => 'test 2']);

        $expected = ['id' => 1, 'name' => 'test 2'];

        $model->name = 'test 3';
        $model->id = 4;

        $this->assertEquals($expected, $model->getOriginals());

        $model->resetOriginals();

        $expected = ['id' => 4, 'name' => 'test 3'];

        $this->assertEquals($expected, $model->getOriginals());
    }

    public function dataSetStateNotVoid()
    {
        return [
            [
                ['id' => null],
                AbstractModel::VOID,
                1,
                AbstractModel::PENDING,
            ],
            [
                ['id' => 10],
                AbstractModel::VOID,
                1,
                AbstractModel::PERSISTED,
            ],
            [
                ['id' => null],
                AbstractModel::PERSISTED,
                0,
                AbstractModel::PERSISTED,
            ],
            [
                ['id' => 10],
                AbstractModel::PENDING,
                0,
                AbstractModel::PENDING,
            ],
        ];
    }

    /**
     * @dataProvider dataSetStateNotVoid
     * @covers CL\LunaCore\Model\AbstractModel::setStateNotVoid
     */
    public function testSetStateNotVoid($parameters, $state, $getIdCalled, $expected)
    {
        $model = $this->getMock('CL\LunaCore\Test\Unit\Model\Model', ['getId'], [$parameters, $state]);
        $model
            ->expects($this->exactly($getIdCalled))
            ->method('getId')
            ->will($this->returnValue($parameters['id']));

        $model->setStateNotVoid();
        $this->assertEquals($expected, $model->getState());
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

        $this->assertEquals(AbstractModel::VOID, $model->getState());
        $this->assertTrue($model->isVoid());
    }

    /**
     * @covers CL\LunaCore\Model\AbstractModel::isPending
     */
    public function testIsPending()
    {
        $model = new Model(null, AbstractModel::VOID);

        $this->assertFalse($model->isPending());
        $model->setState(AbstractModel::PENDING);
        $this->assertTrue($model->isPending());
    }


    /**
     * @covers CL\LunaCore\Model\AbstractModel::isPersisted
     */
    public function testIsPersisted()
    {
        $model = new Model(null, AbstractModel::VOID);

        $this->assertFalse($model->isPersisted());
        $model->setState(AbstractModel::PERSISTED);
        $this->assertTrue($model->isPersisted());
    }

    /**
     * @covers CL\LunaCore\Model\AbstractModel::isDeleted
     */
    public function testIsDeleted()
    {
        $model = new Model(null, AbstractModel::VOID);

        $this->assertFalse($model->isDeleted());
        $model->setState(AbstractModel::DELETED);
        $this->assertTrue($model->isDeleted());
    }

    /**
     * @covers CL\LunaCore\Model\AbstractModel::delete
     */
    public function testDelete()
    {
        $model = new Model(null, AbstractModel::VOID);

        $this->assertFalse($model->isDeleted());
        $model->delete();
        $this->assertTrue($model->isDeleted());
    }

    /**
     * @covers CL\LunaCore\Model\AbstractModel::getId
     * @covers CL\LunaCore\Model\AbstractModel::setId
     */
    public function testGetId()
    {
        $model = new Model();
        $repo = $this->getMock('stdClass', ['getPrimaryKey']);
        $repo
            ->expects($this->exactly(4))
            ->method('getPrimaryKey')
            ->will($this->returnValue('id'));

        $model->setRepo($repo);

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
        $asserts = new Asserts([
            new Present('name'),
            new Present('other'),
        ]);

        $model = new Model();
        $repo = $this->getMock('stdClass', ['getAsserts']);
        $repo
            ->expects($this->once())
            ->method('getAsserts')
            ->will($this->returnValue($asserts));

        $model->setRepo($repo);

        $this->assertInstanceOf('CL\Carpo\Errors', $model->getErrors());
        $this->assertCount(0, $model->getErrors());
        $this->assertTrue($model->isEmptyErrors());

        $model->name = null;

        $result = $model->validate();
        $this->assertFalse($result);

        $this->assertInstanceOf('CL\Carpo\Errors', $model->getErrors());
        $this->assertCount(2, $model->getErrors());
        $this->assertFalse($model->isEmptyErrors());
        $this->assertCount(1, $model->getErrors()->onlyFor('name'));
        $this->assertCount(1, $model->getErrors()->onlyFor('other'));
    }
}
