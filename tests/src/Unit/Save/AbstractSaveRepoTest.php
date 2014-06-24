<?php

namespace Harp\Core\Test\Unit\Save;

use Harp\Core\Test\AbstractTestCase;
use Harp\Core\Repo\LinkOne;
use Harp\Core\Repo\Event;
use Harp\Core\Model\State;
use Harp\Core\Model\Models;
use Harp\Core\Save\Save;

/**
 * @coversDefaultClass Harp\Core\Save\AbstractSaveRepo
 *
 * @author     Ivan Kerin <ikerin@gmail.com>
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://spdx.org/licenses/BSD-3-Clause
 */
class AbstractSaveRepoTest extends AbstractTestCase
{
    private $repo;
    private $find;

    public function setUp()
    {
        parent::setUp();

        $this->repo = $this->getMock(__NAMESPACE__.'\Repo', ['findAll', 'newSave', 'loadRelFor']);
        $this->find = $this->getMock(__NAMESPACE__.'\Find', ['where', 'limit', 'execute'], [$this->repo]);

        $this->repo
            ->expects($this->any())
            ->method('findAll')
            ->will($this->returnValue($this->find));
    }

    /**
     * @covers ::find
     */
    public function testFind()
    {
        $model = new Model();

        $this->find
            ->expects($this->exactly(2))
            ->method('where')
            ->with($this->equalTo('id'), $this->equalTo(4))
            ->will($this->returnSelf());

        $this->find
            ->expects($this->exactly(2))
            ->method('limit')
            ->with($this->equalTo(1))
            ->will($this->returnSelf());

        $this->find
            ->expects($this->exactly(2))
            ->method('execute')
            ->will($this->onConsecutiveCalls([$model], []));

        $result = $this->repo->find(4);
        $this->assertSame($model, $result);

        $result = $this->repo->find(4);
        $this->assertInstanceOf(__NAMESPACE__.'\Model', $result);
        $this->assertTrue($result->isVoid());
    }

    /**
     * @covers ::findByName
     */
    public function testfindByName()
    {
        $model = new Model();

        $this->find
            ->expects($this->exactly(2))
            ->method('where')
            ->with($this->equalTo('name'), $this->equalTo('test'))
            ->will($this->returnSelf());

        $this->find
            ->expects($this->exactly(2))
            ->method('limit')
            ->with($this->equalTo(1))
            ->will($this->returnSelf());

        $this->find
            ->expects($this->exactly(2))
            ->method('execute')
            ->will($this->onConsecutiveCalls([$model], []));

        $result = $this->repo->findByName('test');
        $this->assertSame($model, $result);

        $result = $this->repo->findByName('test');
        $this->assertInstanceOf(__NAMESPACE__.'\Model', $result);
        $this->assertTrue($result->isVoid());
    }

    /**
     * @covers ::newSave
     */
    public function testNewSave()
    {
        $save = Repo::get()->newSave();

        $this->assertInstanceOf('Harp\Core\Save\Save', $save);
    }

    /**
     * @covers ::save
     */
    public function testSave()
    {
        $save = $this->getMock('Harp\Core\Save\Save', ['execute', 'add']);
        $model = new Model();

        $save
            ->expects($this->once())
            ->method('add')
            ->with($this->identicalTo($model))
            ->will($this->returnSelf());

        $save
            ->expects($this->once())
            ->method('execute')
            ->will($this->returnSelf());

        $this->repo
            ->expects($this->once())
            ->method('newSave')
            ->will($this->returnValue($save));

        $this->repo->save($model);
    }

    /**
     * @covers ::saveArray
     */
    public function testSaveArray()
    {
        $save = $this->getMock('Harp\Core\Save\Save', ['execute', 'addArray']);
        $models = [new Model()];

        $save
            ->expects($this->once())
            ->method('addArray')
            ->with($this->identicalTo($models))
            ->will($this->returnSelf());

        $save
            ->expects($this->once())
            ->method('execute')
            ->will($this->returnSelf());

        $this->repo
            ->expects($this->once())
            ->method('newSave')
            ->will($this->returnValue($save));

        $this->repo->saveArray($models);
    }

    /**
     * @covers ::save
     * @expectedException InvalidArgumentException
     */
    public function testSaveOtherModel()
    {
        $otherModel = new SoftDeleteModel();

        $this->repo->save($otherModel);
    }

    /**
     * @covers ::saveArray
     * @expectedException InvalidArgumentException
     */
    public function testSaveArrayOtherModel()
    {
        $models = [new Model(), new SoftDeleteModel()];

        $this->repo->saveArray($models);
    }

    /**
     * @covers ::addLink
     * @covers ::loadLink
     */
    public function testAddLink()
    {
        $model = new Model();
        $foreign = new Model();
        $link = new LinkOne($model, Repo::get()->getRel('one'), $foreign);

        Repo::get()->addLink($link);

        $this->assertSame($link, Repo::get()->loadLink($model, 'one'));
    }

    /**
     * @covers ::loadLink
     */
    public function testLoadLink()
    {
        $model = new Model();

        $this->repo->addRels([
            new RelOne('test', $this->repo, $this->repo),
        ]);

        $this->repo
            ->expects($this->once())
            ->method('loadRelFor')
            ->with(
                $this->callback(function (Models $models) use ($model) {
                    return $models->toArray() === [$model];
                }),
                $this->equalTo('test'),
                $this->equalTo(State::DELETED)
            );

        $this->repo->loadLink($model, 'test', State::DELETED);
    }


    /**
     * @covers ::loadRelFor
     */
    public function testLoadRelFor()
    {
        $repo = new Repo();

        $modelsSource = [new Model(['repo' => $repo]), new Model(['repo' => $repo])];
        $foreignSource = [new Model(['repo' => $repo]), new Model(['repo' => $repo])];

        $models = new Models($modelsSource);
        $foreign = new Models($foreignSource);

        $rel = $this->getMock(
            __NAMESPACE__.'\RelOne',
            ['loadForeignModels', 'areLinked'],
            ['test', $repo, $repo]
        );

        $repo->addRels([$rel]);

        $rel
            ->expects($this->once())
            ->method('loadForeignModels')
            ->with($this->identicalTo($models), $this->equalTo(State::DELETED))
            ->will($this->returnValue($foreign));

        $map = [
            [$modelsSource[0], $foreignSource[0], true],
            [$modelsSource[1], $foreignSource[0], false],
            [$modelsSource[0], $foreignSource[1], false],
            [$modelsSource[1], $foreignSource[1], true],
        ];

        $rel
            ->expects($this->exactly(4))
            ->method('areLinked')
            ->will($this->returnValueMap($map));

        $result = $repo->loadRelFor($models, 'test', State::DELETED);

        $this->assertSame($foreign, $result);

        $link1 = $repo->loadLink($modelsSource[0], 'test');
        $link2 = $repo->loadLink($modelsSource[1], 'test');

        $this->assertSame($foreignSource[0], $link1->get());
        $this->assertSame($foreignSource[1], $link2->get());
    }

    /**
     * @covers ::loadAllRelsFor
     */
    public function testLoadAllRelsFor()
    {
        $repo1 = $this->getMock(__NAMESPACE__.'\Repo', ['loadRelFor']);
        $repo2 = $this->getMock(__NAMESPACE__.'\Repo', ['loadRelFor']);
        $repo3 = $this->getMock(__NAMESPACE__.'\Repo', ['loadRelFor']);

        $repo1->addRel(new RelOne('one', $repo1, $repo2));
        $repo2->addRel(new RelMany('many', $repo2, $repo3));

        $models1 = new Models([new Model(['repo' => $repo1])]);
        $models2 = new Models([new Model(['repo' => $repo2])]);
        $models3 = new Models([new Model(['repo' => $repo3])]);

        $repo1
            ->expects($this->once())
            ->method('loadRelFor')
            ->with($this->equalTo($models1), $this->equalTo('one'), $this->equalTo(State::DELETED))
            ->will($this->returnValue($models2));

        $repo2
            ->expects($this->once())
            ->method('loadRelFor')
            ->with($this->equalTo($models2), $this->equalTo('many'), $this->equalTo(State::DELETED))
            ->will($this->returnValue($models3));

        $repo1->loadAllRelsFor($models1, ['one' => 'many'], State::DELETED);
    }

    /**
     * @covers ::updateModels
     */
    public function testUpdateModels()
    {
        $repo = $this->getMock(
            __NAMESPACE__.'\Repo',
            ['update', 'dispatchBeforeEvent', 'dispatchAfterEvent']
        );

        $models = [
            new Model(null, State::SAVED),
            new SoftDeleteModel(['deletedAt' => time()], State::DELETED)
        ];

        $modelsObject = new Models($models);

        $repo
            ->expects($this->once())
            ->method('update')
            ->with($this->identicalTo($modelsObject));

        $repo
            ->expects($this->at(0))
            ->method('dispatchBeforeEvent')
            ->with($this->identicalTo($models[0], $this->equalTo(Event::UPDATE)));

        $repo
            ->expects($this->at(1))
            ->method('dispatchBeforeEvent')
            ->with($this->identicalTo($models[0], $this->equalTo(Event::SAVE)));

        $repo
            ->expects($this->at(2))
            ->method('dispatchBeforeEvent')
            ->with($this->identicalTo($models[1], $this->equalTo(Event::DELETE)));

        $repo
            ->expects($this->at(4))
            ->method('dispatchAfterEvent')
            ->with($this->identicalTo($models[0], $this->equalTo(Event::UPDATE)));

        $repo
            ->expects($this->at(5))
            ->method('dispatchAfterEvent')
            ->with($this->identicalTo($models[0], $this->equalTo(Event::SAVE)));

        $repo
            ->expects($this->at(6))
            ->method('dispatchAfterEvent')
            ->with($this->identicalTo($models[1], $this->equalTo(Event::DELETE)));

        $repo->updateModels($modelsObject);
    }

    /**
     * @covers ::deleteModels
     */
    public function testDeleteModels()
    {
        $repo = $this->getMock(
            __NAMESPACE__.'\Repo',
            ['delete', 'dispatchBeforeEvent', 'dispatchAfterEvent']
        );

        $models = [new Model(null, State::DELETED), new Model(null, State::DELETED)];
        $modelsObject = new Models($models);

        $repo
            ->expects($this->once())
            ->method('delete')
            ->with($this->identicalTo($modelsObject));

        $repo
            ->expects($this->at(0))
            ->method('dispatchBeforeEvent')
            ->with($this->identicalTo($models[0], $this->equalTo(Event::DELETE)));

        $repo
            ->expects($this->at(1))
            ->method('dispatchBeforeEvent')
            ->with($this->identicalTo($models[1], $this->equalTo(Event::DELETE)));

        $repo
            ->expects($this->at(3))
            ->method('dispatchAfterEvent')
            ->with($this->identicalTo($models[0], $this->equalTo(Event::DELETE)));

        $repo
            ->expects($this->at(4))
            ->method('dispatchAfterEvent')
            ->with($this->identicalTo($models[1], $this->equalTo(Event::DELETE)));

        $repo->deleteModels($modelsObject);
    }

    /**
     * @covers ::insertModels
     */
    public function testInsertModels()
    {
        $repo = $this->getMock(
            __NAMESPACE__.'\Repo',
            ['insert', 'dispatchBeforeEvent', 'dispatchAfterEvent']
        );

        $models = [new Model(), new Model()];
        $modelsObject = new Models($models);

        $repo
            ->expects($this->once())
            ->method('insert')
            ->with($this->identicalTo($modelsObject));

        $repo
            ->expects($this->at(0))
            ->method('dispatchBeforeEvent')
            ->with($this->identicalTo($models[0], $this->equalTo(Event::INSERT)));

        $repo
            ->expects($this->at(1))
            ->method('dispatchBeforeEvent')
            ->with($this->identicalTo($models[0], $this->equalTo(Event::SAVE)));

        $repo
            ->expects($this->at(2))
            ->method('dispatchBeforeEvent')
            ->with($this->identicalTo($models[1], $this->equalTo(Event::INSERT)));

        $repo
            ->expects($this->at(3))
            ->method('dispatchBeforeEvent')
            ->with($this->identicalTo($models[1], $this->equalTo(Event::SAVE)));

        $repo
            ->expects($this->at(5))
            ->method('dispatchAfterEvent')
            ->with($this->identicalTo($models[0], $this->equalTo(Event::INSERT)));

        $repo
            ->expects($this->at(6))
            ->method('dispatchAfterEvent')
            ->with($this->identicalTo($models[0], $this->equalTo(Event::SAVE)));

        $repo
            ->expects($this->at(7))
            ->method('dispatchAfterEvent')
            ->with($this->identicalTo($models[1], $this->equalTo(Event::INSERT)));

        $repo
            ->expects($this->at(8))
            ->method('dispatchAfterEvent')
            ->with($this->identicalTo($models[1], $this->equalTo(Event::SAVE)));

        $repo->insertModels($modelsObject);

        foreach ($models as $model) {
            $this->assertTrue($model->isSaved());
        }
    }
}
