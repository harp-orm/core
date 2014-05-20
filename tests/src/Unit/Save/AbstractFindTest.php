<?php

namespace CL\LunaCore\Test\Unit\Save;

use CL\LunaCore\Test\AbstractTestCase;
use CL\LunaCore\Model\State;
use CL\LunaCore\Model\Models;
use CL\LunaCore\Save\AbstractFind;

class AbstractFindTest extends AbstractTestCase
{
    /**
     * @covers CL\LunaCore\Save\AbstractFind::__construct
     * @covers CL\LunaCore\Save\AbstractFind::getRepo
     */
    public function testConstruct()
    {
        $repo = Repo::get();
        $find = new Find($repo);

        $this->assertSame($repo, $find->getRepo());
    }

    /**
     * @covers CL\LunaCore\Save\AbstractFind::whereKey
     */
    public function testWhereKey()
    {
        $find = $this->getMock(Find::class, ['where'], [Repo::get()]);

        $find
            ->expects($this->once())
            ->method('where')
            ->with($this->equalTo('id'), $this->equalTo('val'));

        $find->whereKey('val');
    }

    /**
     * @covers CL\LunaCore\Save\AbstractFind::whereKeys
     */
    public function testWhereKeys()
    {
        $find = $this->getMock(Find::class, ['whereIn'], [Repo::get()]);

        $find
            ->expects($this->once())
            ->method('whereIn')
            ->with($this->equalTo('id'), $this->equalTo([2, 5]));

        $find->whereKeys([2, 5]);
    }

    /**
     * @covers CL\LunaCore\Save\AbstractFind::loadRaw
     */
    public function testLoadRaw()
    {
        $repo = new Repo(Model::class);
        $repo->setSoftDelete(true);
        $models1 = [new Model()];
        $models2 = [new Model()];
        $models3 = [new Model()];

        $find = $this->getMock(Find::class, ['execute', 'where', 'whereNot'], [$repo]);

        $find
            ->expects($this->at(0))
            ->method('where')
            ->with($this->equalTo('deletedAt'), null);

        $find
            ->expects($this->at(2))
            ->method('whereNot')
            ->with($this->equalTo('deletedAt'), null);


        $find
            ->expects($this->exactly(3))
            ->method('execute')
            ->will($this->onConsecutiveCalls($models1, $models2, $models3));

        $this->assertSame($models1, $find->loadRaw());
        $this->assertSame($models2, $find->loadRaw(State::DELETED));
        $this->assertSame($models3, $find->loadRaw(State::DELETED | State::SAVED));
    }
    /**
     * @covers CL\LunaCore\Save\AbstractFind::loadRaw
     * @expectedException InvalidArgumentException
     */
    public function testLoadRawInvalidArguments()
    {
        $repo = new Repo(Model::class);
        $repo->setSoftDelete(true);

        $find = new Find($repo);

        $find->loadRaw(State::PENDING);
    }

    /**
     * @covers CL\LunaCore\Save\AbstractFind::load
     */
    public function testLoad()
    {
        $repo = Repo::get();

        $model1 = new Model(['id' => 10], State::SAVED);
        $model2 = new Model(['id' => 10], State::SAVED);
        $model3 = new Model(['id' => 4], State::SAVED);
        $model4 = new Model(['id' => 4], State::SAVED);

        $find = $this->getMock(Find::class, ['loadRaw'], [$repo]);

        $find
            ->expects($this->exactly(2))
            ->method('loadRaw')
            ->with($this->equalTo(State::DELETED | State::SAVED))
            ->will($this->onConsecutiveCalls([$model1, $model3], [$model2, $model4]));

        $loaded = $find->load(State::DELETED | State::SAVED);
        $this->assertInstanceOf(Models::class, $loaded);

        $this->assertSame([$model1, $model3], $loaded->toArray());

        $loaded = $find->load(State::DELETED | State::SAVED);
        $this->assertInstanceOf(Models::class, $loaded);

        $this->assertSame([$model1, $model3], $loaded->toArray());
    }

    /**
     * @covers CL\LunaCore\Save\AbstractFind::loadWith
     */
    public function testLoadWith()
    {
        $repo = $this->getMock(Repo::class, ['loadAllRelsFor'], [Model::class]);
        $find = $this->getMock(Find::class, ['load'], [$repo]);

        $rels = ['one' => 'many'];

        $models = new Models([new Model()]);

        $find
            ->expects($this->once())
            ->method('load')
            ->with($this->equalTo(State::DELETED))
            ->will($this->returnValue($models));

        $repo
            ->expects($this->once())
            ->method('loadAllRelsFor')
            ->with($this->identicalTo($models), $this->equalTo($rels), $this->equalTo(State::DELETED));

        $result = $find->loadWith($rels, State::DELETED);

        $this->assertSame($models, $result);
    }

    /**
     * @covers CL\LunaCore\Save\AbstractFind::loadIds
     */
    public function testLoadIds()
    {
        $find = $this->getMock(Find::class, ['load'], [Repo::get()]);

        $models = new Models([
            new Model(['id' => 4]),
            new Model(['id' => 98]),
            new Model(['id' => 100]),
        ]);

        $find
            ->expects($this->once())
            ->method('load')
            ->with($this->equalTo(State::DELETED))
            ->will($this->returnValue($models));

        $expected = [4, 98, 100];

        $result = $find->loadIds(State::DELETED);

        $this->assertSame($expected, $result);
    }

    /**
     * @covers CL\LunaCore\Save\AbstractFind::loadCount
     */
    public function testLoadCount()
    {
        $find = $this->getMock(Find::class, ['loadRaw'], [Repo::get()]);

        $models = [
            new Model(['id' => 4]),
            new Model(['id' => 98]),
            new Model(['id' => 100]),
        ];

        $find
            ->expects($this->once())
            ->method('loadRaw')
            ->with($this->equalTo(State::DELETED))
            ->will($this->returnValue($models));

        $result = $find->loadCount(State::DELETED);

        $this->assertEquals(3, $result);
    }


    /**
     * @covers CL\LunaCore\Save\AbstractFind::loadFirst
     */
    public function testLoadFirst()
    {
        $repo = new Repo(Model::class);
        $find = $this->getMock(Find::class, ['limit', 'load'], [$repo]);

        $model = new Model(['id' => 300, 'repo' => $repo]);
        $models = new Models([$model]);
        $emptyModels = new Models();

        $find
            ->expects($this->exactly(2))
            ->method('limit')
            ->with($this->equalTo(1))
            ->will($this->returnSelf());

        $find
            ->expects($this->exactly(2))
            ->method('load')
            ->with($this->equalTo(State::DELETED))
            ->will($this->onConsecutiveCalls($models, $emptyModels));

        $result = $find->loadFirst(State::DELETED);

        $this->assertSame($model, $result);

        $result = $find->loadFirst(State::DELETED);

        $this->assertInstanceOf(Model::class, $result);
        $this->assertTrue($result->isVoid());
    }
}
