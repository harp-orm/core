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
use Harp\Serializer\Json;

/**
 * @coversDefaultClass Harp\Core\Repo\AbstractRepo
 *
 * @author     Ivan Kerin <ikerin@gmail.com>
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://spdx.org/licenses/BSD-3-Clause
 */
class AbstractRepoTest extends AbstractRepoTestCase
{
    /**
     * @covers ::getName
     */
    public function testGetName()
    {
        $repo = new Repo(__NAMESPACE__.'\Model');

        $this->assertEquals('Model', $repo->getName());
    }

    /**
     * @covers ::getPrimaryKey
     * @covers ::setPrimaryKey
     */
    public function testPrimaryKey()
    {
        $repo = new Repo(__NAMESPACE__.'\Model');

        $this->assertEquals('id', $repo->getPrimaryKey());

        $repo->setPrimaryKey('guid');

        $this->assertEquals('guid', $repo->getPrimaryKey());
    }

    /**
     * @covers ::initializeModel
     */
    public function testInitializeModel()
    {
        $repo = new Repo(__NAMESPACE__.'\Model');
        $repo
            ->setInherited(true)
            ->addSerializers([new Json('name')])
            ->addEventAfter(Event::CONSTRUCT, function($model) {
                $model->constructCalled = true;
            });

        $model = $this->getMockForAbstractClass(
            __NAMESPACE__.'\Model',
            [],
            '',
            false
        );

        $model->name = '{"test":"test2"}';

        $repo->initializeModel($model);

        $this->assertEquals('Harp\Core\Test\Unit\Repo\Model', $model->class);
        $this->assertEquals(['test' => 'test2'], $model->name);
    }

    /**
     * @covers ::getRootRepo
     * @covers ::setRootRepo
     */
    public function testRootRepo()
    {
        $repo = new Repo(__NAMESPACE__.'\Model');

        $this->assertSame($repo, $repo->getRootRepo());

        $repo->setRootRepo(ModelInherited::getRepo());

        $this->assertSame(ModelInherited::getRepo(), $repo->getRootRepo());
    }

    /**
     * @covers ::setRootRepo
     * @expectedException LogicException
     * @expectedExceptionMessage The root repo must be set as inherited (->setInherited(true))
     */
    public function testRootRepoError1()
    {
        $repo = new Repo(__NAMESPACE__.'\ModelOther');

        $repo->setRootRepo(ModelOther::getRepo());
    }

    /**
     * @covers ::setRootRepo
     * @expectedException LogicException
     * @expectedExceptionMessage You must call parent::initialize() for inherited repos
     */
    public function testRootRepoError2()
    {
        $repo = new RepoOther(__NAMESPACE__.'\ModelOther');

        $repo->setRootRepo(Model::getRepo());
    }

    /**
     * @covers ::getNameKey
     * @covers ::setNameKey
     */
    public function testNameKey()
    {
        $repo = new Repo(__NAMESPACE__.'\Model');

        $this->assertEquals('name', $repo->getNameKey());

        $repo->setNameKey('title');

        $this->assertEquals('title', $repo->getNameKey());
    }

    /**
     * @covers ::getSoftDelete
     * @covers ::setSoftDelete
     */
    public function testSoftDelete()
    {
        $repo = new Repo(__NAMESPACE__.'\Model');

        $this->assertEquals(false, $repo->getSoftDelete());

        $repo->setSoftDelete(true);

        $this->assertEquals(true, $repo->getSoftDelete());
    }

    /**
     * @covers ::getModelClass
     * @covers ::setModelClass
     */
    public function testModelClass()
    {
        $repo = new Repo(__NAMESPACE__.'\Model');

        $this->assertEquals(__NAMESPACE__.'\Model', $repo->getModelClass());

        $repo->setModelClass(__NAMESPACE__.'\ModelOther');

        $this->assertEquals(__NAMESPACE__.'\ModelOther', $repo->getModelClass());
    }

    /**
     * @covers ::getInherited
     * @covers ::setInherited
     */
    public function testInherited()
    {
        $repo = new RepoOther(__NAMESPACE__.'\ModelOther');

        $this->assertEquals(false, $repo->getInherited());

        $repo->setInherited(true);

        $this->assertEquals(true, $repo->getInherited());
    }

    /**
     * @covers ::getLinkMap
     */
    public function testGetLinkMap()
    {
        $repo = new Repo(__NAMESPACE__.'\Model');

        $this->assertInstanceof('Harp\Core\Repo\LinkMap', $repo->getLinkMap());
    }

    /**
     * @covers ::getIdentityMap
     */
    public function testGetIdentityMap()
    {
        $repo = new Repo(__NAMESPACE__.'\Model');

        $this->assertInstanceof('Harp\Core\Repo\IdentityMap', $repo->getIdentityMap());
    }

    /**
     * @covers ::getModelReflection
     */
    public function testGetModelReflection()
    {
        $repo = new Repo(__NAMESPACE__.'\Model');

        $this->assertInstanceof('ReflectionClass', $repo->getModelReflection());
        $this->assertEquals(__NAMESPACE__.'\Model', $repo->getModelReflection()->getName());
    }

    /**
     * @covers ::isModel
     */
    public function testIsModel()
    {
        $repo = Model::getRepo();
        $repoInherited = ModelInherited::getRepo();
        $model = new Model();
        $modelInherited = new Model();

        $this->assertTrue($repo->isModel($model));
        $this->assertTrue($repo->isModel($modelInherited));
        $this->assertTrue($repoInherited->isModel($modelInherited));
        $this->assertTrue($repoInherited->isModel($model));

        $model = new ModelOther();

        $this->assertFalse($repo->isModel($model));
    }

    /**
     * @covers ::assertModel
     */
    public function testAssertModel()
    {
        $repo = new Repo(__NAMESPACE__.'\Model');
        $model = new Model();
        $other = new ModelOther();

        $repo->assertModel($model);

        $this->setExpectedException('InvalidArgumentException');

        $repo->assertModel($other);
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
        $repo = new Repo(__NAMESPACE__.'\Model');

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
     * @covers ::getAsserts
     * @covers ::addAsserts
     */
    public function testAsserts()
    {
        $repo = new Repo(__NAMESPACE__.'\Model');

        $this->assertInstanceof('Harp\Validate\Asserts', $repo->getAsserts());

        $asserts = [
            new Present('name'),
        ];

        $repo->addAsserts($asserts);

        $this->assertSame($asserts, Objects::toArray($repo->getAsserts()->all()));
    }

    /**
     * @covers ::getSerializers
     * @covers ::addSerializers
     */
    public function testSerializers()
    {
        $repo = new Repo(__NAMESPACE__.'\Model');

        $this->assertInstanceof('Harp\Serializer\Serializers', $repo->getSerializers());

        $serializers = [
            new Json('profile'),
        ];

        $repo->addSerializers($serializers);

        $this->assertSame($serializers, Objects::toArray($repo->getSerializers()->all()));
    }

    /**
     * @covers ::getEventListeners
     * @covers ::addEventBefore
     * @covers ::addEventAfter
     */
    public function testEventListeners()
    {
        $repo = new Repo(__NAMESPACE__.'\Model');

        $this->assertInstanceof('Harp\Core\Repo\EventListeners', $repo->getEventListeners());

        $repo
            ->addEventBefore(Event::SAVE, 'before save callback')
            ->addEventBefore(Event::INSERT, 'before insert callback')
            ->addEventAfter(Event::DELETE, 'after delete callback');

        $expectedBefore = [
          Event::SAVE   => ['before save callback'],
          Event::INSERT => ['before insert callback'],
        ];

        $expectedAfter = [
          Event::DELETE => ['after delete callback'],
        ];

        $this->assertEquals($expectedBefore, $repo->getEventListeners()->getBefore());
        $this->assertEquals($expectedAfter, $repo->getEventListeners()->getAfter());
    }


    /**
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
            ->expects($this->exactly(2))
            ->method('getEventListeners')
            ->will($this->returnValue($eventListener));

        $eventListener
            ->expects($this->once())
            ->method('dispatchBeforeEvent')
            ->with($this->identicalTo($model), $this->equalTo(Event::SAVE));

        $repo->dispatchBeforeEvent($model, Event::SAVE);

        $eventListener
            ->expects($this->once())
            ->method('dispatchAfterEvent')
            ->with($this->identicalTo($model), $this->equalTo(Event::SAVE));

        $repo->dispatchAfterEvent($model, Event::SAVE);
    }

    /**
     * @covers ::newModel
     */
    public function testNewModel()
    {
        $repo = new Repo(__NAMESPACE__.'\Model');

        $model = $repo->newModel();

        $this->assertInstanceOf(__NAMESPACE__.'\Model', $model);
        $this->assertEquals(['id' => null, 'name' => 'test', 'class' => __NAMESPACE__.'\Model'], $model->getProperties());
        $this->assertTrue($model->isPending());

        $model = $repo->newModel(['id' => 10, 'name' => 'new'], State::SAVED);

        $this->assertEquals(['id' => 10, 'name' => 'new', 'class' => __NAMESPACE__.'\Model'], $model->getProperties());
        $this->assertTrue($model->isSaved());
    }

    /**
     * @covers ::newVoidModel
     */
    public function testNewVoidModel()
    {
        $repo = new Repo(__NAMESPACE__.'\Model');

        $model = $repo->newVoidModel();

        $this->assertInstanceOf(__NAMESPACE__.'\Model', $model);
        $this->assertEquals(['id' => null, 'name' => 'test', 'class' => __NAMESPACE__.'\Model'], $model->getProperties());
        $this->assertTrue($model->isVoid());

        $model = $repo->newVoidModel(['id' => 10, 'name' => 'new']);

        $this->assertEquals(['id' => 10, 'name' => 'new', 'class' => __NAMESPACE__.'\Model'], $model->getProperties());
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

        $repo->initializeOnce();

        $this->assertTrue($repo->getInitialized(), 'Should remaind initialized, but initializeAll Should be called only once');
    }
}
