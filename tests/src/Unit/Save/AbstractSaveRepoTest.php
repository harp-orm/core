<?php

namespace CL\LunaCore\Test\Unit\Save;

use CL\LunaCore\Test\AbstractTestCase;
use CL\LunaCore\Repo\LinkOne;
use CL\LunaCore\Repo\Event;
use CL\LunaCore\Model\State;
use CL\LunaCore\Model\Models;
use CL\LunaCore\Save\Save;

/**
 * @coversDefaultClass CL\LunaCore\Save\AbstractSaveRepo
 */
class AbstractSaveRepoTest extends AbstractTestCase
{
    private $repo;
    private $find;

    public function setUp()
    {
        parent::setUp();

        $this->repo = $this->getMock(__NAMESPACE__.'\Repo', ['findAll', 'newSave', 'loadRelFor'], [__NAMESPACE__.'\Model']);
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
     * @covers ::newSave
     */
    public function testNewSave()
    {
        $save = Repo::get()->newSave();

        $this->assertInstanceOf('CL\LunaCore\Save\Save', $save);
    }

    /**
     * @covers ::save
     */
    public function testSave()
    {
        $save = $this->getMock('CL\LunaCore\Save\Save', ['execute', 'add']);
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
     * @covers ::save
     * @expectedException InvalidArgumentException
     */
    public function testSaveOtherModel()
    {
        $otherModel = new SoftDeleteModel();

        $this->repo->save($otherModel);
    }

    /**
     * @covers ::addLink
     * @covers ::loadLink
     */
    public function testAddLink()
    {
        $model = new Model();
        $foreign = new Model();
        $link = new LinkOne(Repo::get()->getRel('one'), $foreign);

        Repo::get()->addLink($model, $link);

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
        $repo = new Repo(__NAMESPACE__.'\Model');

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
        $repo1 = $this->getMock(__NAMESPACE__.'\Repo', ['loadRelFor'], [__NAMESPACE__.'\Model']);
        $repo2 = $this->getMock(__NAMESPACE__.'\Repo', ['loadRelFor'], [__NAMESPACE__.'\Model']);
        $repo3 = $this->getMock(__NAMESPACE__.'\Repo', ['loadRelFor'], [__NAMESPACE__.'\Model']);

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
            ['update', 'dispatchBeforeEvent', 'dispatchAfterEvent'],
            [__NAMESPACE__.'\Model']
        );

        $models = [new Model(null, State::SAVED), new Model(['deletedAt' => time()], State::DELETED)];
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
            ->expects($this->at(5))
            ->method('dispatchAfterEvent')
            ->with($this->identicalTo($models[0], $this->equalTo(Event::UPDATE)));

        $repo
            ->expects($this->at(6))
            ->method('dispatchAfterEvent')
            ->with($this->identicalTo($models[0], $this->equalTo(Event::SAVE)));

        $repo
            ->expects($this->at(7))
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
            ['delete', 'dispatchBeforeEvent', 'dispatchAfterEvent'],
            [__NAMESPACE__.'\Model']
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
            ['insert', 'dispatchBeforeEvent', 'dispatchAfterEvent'],
            [__NAMESPACE__.'\Model']
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
