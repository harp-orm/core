<?php

namespace Harp\Core\Test\Unit\Repo;

use Harp\Core\Repo\AbstractRepo;
use Harp\Core\Repo\Links;
use Harp\Core\Repo\LinkOne;
use Harp\Core\Repo\LinkMap;
use Harp\Core\Repo\Event;
use Harp\Core\Test\Model\User;
use Harp\Core\Model\AbstractModel;
use Harp\Core\Model\State;
use Harp\Util\Objects;
use Harp\Validate\Assert\Present;

/**
 * @coversDefaultClass Harp\Core\Repo\AbstractRepo
 */
class AbstractRepoTest extends AbstractRepoTestCase
{
    public function getRepoInitialized($initialized)
    {
        $repo = $this->getMockForAbstractClass(
            'Harp\Core\Repo\AbstractRepo',
            [__NAMESPACE__.'\Model']
        );

        $repo
            ->expects($initialized ? $this->once() : $this->never())
            ->method('initialize');

        return $repo;
    }

    /**
     * @covers ::__construct
     * @covers ::getModelClass
     */
    public function testConstruct()
    {
        $repo = new Repo(__NAMESPACE__.'\Model');

        $this->assertEquals(__NAMESPACE__.'\Model', $repo->getModelClass());
    }

    /**
     * @covers ::getName
     */
    public function testGetName()
    {
        $repo = $this->getRepoInitialized(false);

        $this->assertEquals('Model', $repo->getName());
    }

    /**
     * @covers ::getPrimaryKey
     * @covers ::setPrimaryKey
     */
    public function testPrimaryKey()
    {
        $repo = $this->getRepoInitialized(true);

        $this->assertEquals('id', $repo->getPrimaryKey());

        $repo->setPrimaryKey('guid');

        $this->assertEquals('guid', $repo->getPrimaryKey());
    }

    /**
     * @covers ::getSoftDelete
     * @covers ::setSoftDelete
     */
    public function testSoftDelete()
    {
        $repo = $this->getRepoInitialized(true);

        $this->assertEquals(false, $repo->getSoftDelete());

        $repo->setSoftDelete(true);

        $this->assertEquals(true, $repo->getSoftDelete());
    }

    /**
     * @covers ::getInherited
     * @covers ::setInherited
     */
    public function testInherited()
    {
        $repo = $this->getRepoInitialized(true);

        $this->assertEquals(false, $repo->getInherited());

        $repo->setInherited(true);

        $this->assertEquals(true, $repo->getInherited());
    }

    /**
     * @covers ::getLinkMap
     */
    public function testGetLinkMap()
    {
        $repo = $this->getRepoInitialized(false);

        $this->assertInstanceof('Harp\Core\Repo\LinkMap', $repo->getLinkMap());
    }

    /**
     * @covers ::getIdentityMap
     */
    public function testGetIdentityMap()
    {
        $repo = $this->getRepoInitialized(false);

        $this->assertInstanceof('Harp\Core\Repo\IdentityMap', $repo->getIdentityMap());
    }

    /**
     * @covers ::getModelReflection
     */
    public function testGetModelReflection()
    {
        $repo = $this->getRepoInitialized(false);

        $this->assertInstanceof('ReflectionClass', $repo->getModelReflection());
        $this->assertEquals(__NAMESPACE__.'\Model', $repo->getModelReflection()->getName());
    }

    /**
     * @covers ::isModel
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
     * @covers ::getRels
     * @covers ::getRel
     * @covers ::addRels
     * @covers ::addRel
     * @covers ::getRelOrError
     * @expectedException InvalidArgumentException
     */
    public function testRels()
    {
        $repo = $this->getRepoInitialized(true);

        $this->assertSame([], $repo->getRels());

        $rels = [
            $this->getRelOne(),
            $this->getRelMany()
        ];

        $expected = [
            'one' => $rels[0],
            'many' => $rels[1],
        ];

        $repo->addRels([$rels[0]]);
        $repo->addRel($rels[1]);

        $this->assertSame($expected, $repo->getRels());
        $this->assertSame($expected['one'], $repo->getRel('one'));
        $this->assertSame($expected['one'], $repo->getRelOrError('one'));
        $this->assertNull($repo->getRel('other'));

        $repo->getRelOrError('other');
    }

    /**
     * @covers ::getEventListeners
     * @covers ::addEventBeforeDelete
     * @covers ::addEventBeforeSave
     * @covers ::addEventBeforeInsert
     * @covers ::addEventBeforeUpdate
     * @covers ::addEventAfterDelete
     * @covers ::addEventAfterSave
     * @covers ::addEventAfterInsert
     * @covers ::addEventAfterUpdate
     * @covers ::addEventAfterLoad
     */
    public function testEventListeners()
    {
        $repo = $this->getRepoInitialized(true);

        $this->assertInstanceof('Harp\Core\Repo\EventListeners', $repo->getEventListeners());

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
     * @covers ::getAsserts
     * @covers ::setAsserts
     */
    public function testAsserts()
    {
        $repo = $this->getRepoInitialized(true);

        $this->assertInstanceof('Harp\Validate\Asserts', $repo->getAsserts());

        $asserts = [
            new Present('name'),
        ];

        $repo->setAsserts($asserts);

        $this->assertSame($asserts, Objects::toArray($repo->getAsserts()->all()));
    }

    /**
     * @covers ::hasBeforeEvent
     * @covers ::hasAfterEvent
     * @covers ::dispatchBeforeEvent
     * @covers ::dispatchAfterEvent
     */
    public function testDispatchEvents()
    {
        $model = new Model();

        $eventListener = $this->getMock('Harp\Core\Repo\EventListeners');

        $repo = $this->getMockForAbstractClass(
            'Harp\Core\Repo\AbstractRepo',
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
     * @covers ::newModel
     */
    public function testNewModel()
    {
        $repo = $this->getRepoInitialized(false);

        $model = $repo->newModel();

        $this->assertInstanceOf(__NAMESPACE__.'\Model', $model);
        $this->assertEquals(['id' => null, 'name' => 'test'], $model->getProperties());
        $this->assertTrue($model->isPending());

        $model = $repo->newModel(['id' => 10, 'name' => 'new'], State::SAVED);

        $this->assertEquals(['id' => 10, 'name' => 'new'], $model->getProperties());
        $this->assertTrue($model->isSaved());
    }

    /**
     * @covers ::newVoidModel
     */
    public function testNewVoidModel()
    {
        $repo = $this->getRepoInitialized(false);

        $model = $repo->newVoidModel();

        $this->assertInstanceOf(__NAMESPACE__.'\Model', $model);
        $this->assertEquals(['id' => null, 'name' => 'test'], $model->getProperties());
        $this->assertTrue($model->isVoid());

        $model = $repo->newVoidModel(['id' => 10, 'name' => 'new']);

        $this->assertEquals(['id' => 10, 'name' => 'new'], $model->getProperties());
        $this->assertTrue($model->isVoid());
    }

    public function testClear()
    {
        $repo = new Repo(__NAMESPACE__.'\Model');
        $model = new Model(['id' => 1], State::SAVED);
        $rel = new RelOne('one', $repo, $repo);
        $repo->addRel($rel);

        $repo->getIdentityMap()->get($model);
        $repo->getLinkMap()->get($model, 'one');

        $this->assertTrue($repo->getIdentityMap()->has($model));
        $this->assertTrue($repo->getLinkMap()->has($model));

        $repo->clear();

        $this->assertFalse($repo->getIdentityMap()->has($model));
        $this->assertFalse($repo->getLinkMap()->has($model));
    }

    /**
     * @covers ::getInitialized
     * @covers ::initializeOnce
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
