<?php

namespace CL\LunaCore\Test\Unit\Repo;

use CL\LunaCore\Repo\AbstractRepo;
use CL\LunaCore\Repo\Links;
use CL\LunaCore\Repo\LinkOne;
use CL\LunaCore\Repo\LinkMap;
use CL\LunaCore\Repo\Rels;
use CL\LunaCore\Repo\Event;
use CL\LunaCore\Test\Model\User;
use CL\LunaCore\Model\AbstractModel;
use CL\LunaCore\Model\State;
use CL\Util\Objects;
use CL\Carpo\Assert\Present;

class AbstractRepoTest extends AbstractRepoTestCase
{
    public function getRepoInitialized($initialized)
    {
        $repo = $this->getMockForAbstractClass(
            'CL\LunaCore\Repo\AbstractRepo',
            [__NAMESPACE__.'\Model']
        );

        $repo
            ->expects($initialized ? $this->once() : $this->never())
            ->method('initialize');

        return $repo;
    }

    /**
     * @covers CL\LunaCore\Repo\AbstractRepo::__construct
     * @covers CL\LunaCore\Repo\AbstractRepo::getModelClass
     */
    public function testConstruct()
    {
        $repo = new Repo(__NAMESPACE__.'\Model');

        $this->assertEquals(__NAMESPACE__.'\Model', $repo->getModelClass());
    }

    /**
     * @covers CL\LunaCore\Repo\AbstractRepo::getName
     */
    public function testGetName()
    {
        $repo = $this->getRepoInitialized(false);

        $this->assertEquals('Model', $repo->getName());
    }

    /**
     * @covers CL\LunaCore\Repo\AbstractRepo::getPrimaryKey
     * @covers CL\LunaCore\Repo\AbstractRepo::setPrimaryKey
     */
    public function testPrimaryKey()
    {
        $repo = $this->getRepoInitialized(true);

        $this->assertEquals('id', $repo->getPrimaryKey());

        $repo->setPrimaryKey('guid');

        $this->assertEquals('guid', $repo->getPrimaryKey());
    }

    /**
     * @covers CL\LunaCore\Repo\AbstractRepo::getSoftDelete
     * @covers CL\LunaCore\Repo\AbstractRepo::setSoftDelete
     */
    public function testSoftDelete()
    {
        $repo = $this->getRepoInitialized(true);

        $this->assertEquals(false, $repo->getSoftDelete());

        $repo->setSoftDelete(true);

        $this->assertEquals(true, $repo->getSoftDelete());
    }

    /**
     * @covers CL\LunaCore\Repo\AbstractRepo::getInherited
     * @covers CL\LunaCore\Repo\AbstractRepo::setInherited
     */
    public function testInherited()
    {
        $repo = $this->getRepoInitialized(true);

        $this->assertEquals(false, $repo->getInherited());

        $repo->setInherited(true);

        $this->assertEquals(true, $repo->getInherited());
    }

    /**
     * @covers CL\LunaCore\Repo\AbstractRepo::getLinkMap
     */
    public function testGetLinkMap()
    {
        $repo = $this->getRepoInitialized(false);

        $this->assertInstanceof('CL\LunaCore\Repo\LinkMap', $repo->getLinkMap());
    }

    /**
     * @covers CL\LunaCore\Repo\AbstractRepo::getIdentityMap
     */
    public function testGetIdentityMap()
    {
        $repo = $this->getRepoInitialized(false);

        $this->assertInstanceof('CL\LunaCore\Repo\IdentityMap', $repo->getIdentityMap());
    }

    /**
     * @covers CL\LunaCore\Repo\AbstractRepo::getModelReflection
     */
    public function testGetModelReflection()
    {
        $repo = $this->getRepoInitialized(false);

        $this->assertInstanceof('ReflectionClass', $repo->getModelReflection());
        $this->assertEquals(__NAMESPACE__.'\Model', $repo->getModelReflection()->getName());
    }

    /**
     * @covers CL\LunaCore\Repo\AbstractRepo::isModel
     */
    public function testIsModel()
    {
        $repo = $this->getRepoInitialized(false);
        $model = new Model();

        $this->assertTrue($repo->isModel($model));

        $model = new ModelOther();

        $this->assertFalse($repo->isModel($model));
    }

    /**
     * @covers CL\LunaCore\Repo\AbstractRepo::getRels
     * @covers CL\LunaCore\Repo\AbstractRepo::getRel
     * @covers CL\LunaCore\Repo\AbstractRepo::setRels
     * @covers CL\LunaCore\Repo\AbstractRepo::getRelOrError
     * @expectedException InvalidArgumentException
     */
    public function testRels()
    {
        $repo = $this->getRepoInitialized(true);

        $this->assertInstanceof('CL\LunaCore\Repo\Rels', $repo->getRels());

        $rels = [
            $this->getRelOne(),
            $this->getRelMany()
        ];

        $expected = [
            'one' => $rels[0],
            'many' => $rels[1],
        ];

        $repo->setRels($rels);

        $this->assertSame($expected, $repo->getRels()->all());
        $this->assertSame($expected['one'], $repo->getRel('one'));
        $this->assertSame($expected['one'], $repo->getRelOrError('one'));
        $this->assertNull($repo->getRel('other'));

        $repo->getRelOrError('other');
    }

    /**
     * @covers CL\LunaCore\Repo\AbstractRepo::getEventListeners
     * @covers CL\LunaCore\Repo\AbstractRepo::addEventBeforeDelete
     * @covers CL\LunaCore\Repo\AbstractRepo::addEventBeforeSave
     * @covers CL\LunaCore\Repo\AbstractRepo::addEventBeforeInsert
     * @covers CL\LunaCore\Repo\AbstractRepo::addEventBeforeUpdate
     * @covers CL\LunaCore\Repo\AbstractRepo::addEventAfterDelete
     * @covers CL\LunaCore\Repo\AbstractRepo::addEventAfterSave
     * @covers CL\LunaCore\Repo\AbstractRepo::addEventAfterInsert
     * @covers CL\LunaCore\Repo\AbstractRepo::addEventAfterUpdate
     * @covers CL\LunaCore\Repo\AbstractRepo::addEventAfterLoad
     */
    public function testEventListeners()
    {
        $repo = $this->getRepoInitialized(true);

        $this->assertInstanceof('CL\LunaCore\Repo\EventListeners', $repo->getEventListeners());

        $repo
            ->addEventBeforeDelete('before delete callback')
            ->addEventBeforeSave('before save callback')
            ->addEventBeforeInsert('before insert callback')
            ->addEventBeforeUpdate('before update callback')
            ->addEventAfterDelete('after delete callback')
            ->addEventAfterSave('after save callback')
            ->addEventAfterInsert('after insert callback')
            ->addEventAfterUpdate('after update callback')
            ->addEventAfterLoad('after load callback');

        $expectedBefore = [
          Event::DELETE => ['before delete callback'],
          Event::SAVE   => ['before save callback'],
          Event::INSERT => ['before insert callback'],
          Event::UPDATE => ['before update callback'],
        ];

        $expectedAfter = [
          Event::DELETE => ['after delete callback'],
          Event::SAVE   => ['after save callback'],
          Event::INSERT => ['after insert callback'],
          Event::UPDATE => ['after update callback'],
          Event::LOAD   => ['after load callback'],
        ];

        $this->assertEquals($expectedBefore, $repo->getEventListeners()->getBefore());
        $this->assertEquals($expectedAfter, $repo->getEventListeners()->getAfter());
    }

    /**
     * @covers CL\LunaCore\Repo\AbstractRepo::getAsserts
     * @covers CL\LunaCore\Repo\AbstractRepo::setAsserts
     */
    public function testAsserts()
    {
        $repo = $this->getRepoInitialized(true);

        $this->assertInstanceof('CL\Carpo\Asserts', $repo->getAsserts());

        $asserts = [
            new Present('name'),
        ];

        $repo->setAsserts($asserts);

        $this->assertSame($asserts, Objects::toArray($repo->getAsserts()->all()));
    }

    /**
     * @covers CL\LunaCore\Repo\AbstractRepo::hasBeforeEvent
     * @covers CL\LunaCore\Repo\AbstractRepo::hasAfterEvent
     * @covers CL\LunaCore\Repo\AbstractRepo::dispatchBeforeEvent
     * @covers CL\LunaCore\Repo\AbstractRepo::dispatchAfterEvent
     */
    public function testDispatchEvents()
    {
        $model = new Model();

        $eventListener = $this->getMock('CL\LunaCore\Repo\EventListeners');

        $repo = $this->getMockForAbstractClass(
            'CL\LunaCore\Repo\AbstractRepo',
            [__NAMESPACE__.'\Model'],
            '',
            true,
            true,
            true,
            ['getEventListeners']
        );

        $repo
            ->expects($this->exactly(4))
            ->method('getEventListeners')
            ->will($this->returnValue($eventListener));

        $eventListener
            ->expects($this->once())
            ->method('dispatchBeforeEvent')
            ->with($this->identicalTo($model), $this->equalTo(Event::LOAD));

        $repo->dispatchBeforeEvent($model, Event::LOAD);

        $eventListener
            ->expects($this->once())
            ->method('dispatchAfterEvent')
            ->with($this->identicalTo($model), $this->equalTo(Event::LOAD));

        $repo->dispatchAfterEvent($model, Event::LOAD);

        $eventListener
            ->expects($this->once())
            ->method('hasBeforeEvent')
            ->with($this->equalTo(Event::LOAD));

        $repo->hasBeforeEvent(Event::LOAD);

        $eventListener
            ->expects($this->once())
            ->method('hasAfterEvent')
            ->with($this->equalTo(Event::LOAD));

        $repo->hasAfterEvent(Event::LOAD);
    }

    /**
     * @covers CL\LunaCore\Repo\AbstractRepo::newInstance
     */
    public function testNewInstance()
    {
        $repo = $this->getRepoInitialized(false);

        $model = $repo->newInstance();

        $this->assertInstanceOf(__NAMESPACE__.'\Model', $model);
        $this->assertEquals(['id' => null, 'name' => 'test'], $model->getProperties());
        $this->assertTrue($model->isPending());

        $model = $repo->newInstance(['id' => 10, 'name' => 'new'], State::SAVED);

        $this->assertEquals(['id' => 10, 'name' => 'new'], $model->getProperties());
        $this->assertTrue($model->isSaved());
    }

    /**
     * @covers CL\LunaCore\Repo\AbstractRepo::newVoidInstance
     */
    public function testNewVoidInstance()
    {
        $repo = $this->getRepoInitialized(false);

        $model = $repo->newVoidInstance();

        $this->assertInstanceOf(__NAMESPACE__.'\Model', $model);
        $this->assertEquals(['id' => null, 'name' => 'test'], $model->getProperties());
        $this->assertTrue($model->isVoid());

        $model = $repo->newVoidInstance(['id' => 10, 'name' => 'new']);

        $this->assertEquals(['id' => 10, 'name' => 'new'], $model->getProperties());
        $this->assertTrue($model->isVoid());
    }

    /**
     * @covers CL\LunaCore\Repo\AbstractRepo::getInitialized
     * @covers CL\LunaCore\Repo\AbstractRepo::initializeOnce
     */
    public function testGetInitialized()
    {
        $repo = new Repo(__NAMESPACE__.'\Model');

        $this->assertFalse($repo->getInitialized());

        $repo->initializeOnce();

        $this->assertTrue($repo->getInitialized());
        $this->assertTrue($repo->initializeCalled);

        $repo->initializeOnce();

        $this->assertTrue($repo->getInitialized(), 'Should remaind initialized, but initializeAll Should be called only once');
    }

    /**
     * @coversNothing
     */
    public function testInitialize()
    {
        $repo = new Repo(__NAMESPACE__.'\Model');

        $this->assertFalse($repo->initializeCalled);
        $this->assertFalse($repo->initialize1TraitCalled);
        $this->assertFalse($repo->initialize2TraitCalled);

        $repo->initialize();

        $this->assertTrue($repo->initializeCalled);
        $this->assertTrue($repo->initialize1TraitCalled);
        $this->assertTrue($repo->initialize2TraitCalled);
    }
}
