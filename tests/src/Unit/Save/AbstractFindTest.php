<?php

namespace Harp\Core\Test\Unit\Save;

use Harp\Core\Test\AbstractTestCase;
use Harp\Core\Model\State;
use Harp\Core\Model\Models;
use Harp\Core\Repo\RepoModels;
use Harp\Core\Save\AbstractFind;

/**
 * @coversDefaultClass Harp\Core\Save\AbstractFind
 *
 * @author     Ivan Kerin <ikerin@gmail.com>
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://spdx.org/licenses/BSD-3-Clause
 */
class AbstractFindTest extends AbstractTestCase
{
    /**
     * @covers ::__construct
     * @covers ::getRepo
     */
    public function testConstruct()
    {
        $repo = Model::getRepo();
        $find = new Find($repo);

        $this->assertSame($repo, $find->getRepo());
    }

    /**
     * @covers ::whereKey
     */
    public function testWhereKey()
    {
        $find = $this->getMock(__NAMESPACE__.'\Find', ['where'], [Model::getRepo()]);

        $find
            ->expects($this->once())
            ->method('where')
            ->with($this->equalTo('id'), $this->equalTo('val'));

        $find->whereKey('val');
    }

    /**
     * @covers ::whereKeys
     */
    public function testWhereKeys()
    {
        $find = $this->getMock(__NAMESPACE__.'\Find', ['whereIn'], [Model::getRepo()]);

        $find
            ->expects($this->once())
            ->method('whereIn')
            ->with($this->equalTo('id'), $this->equalTo([2, 5]));

        $find->whereKeys([2, 5]);
    }

    /**
     * @covers ::loadRaw
     */
    public function testLoadRaw()
    {
        $repo = new Repo(__NAMESPACE__.'\Model');

        $models = [new Model(), new Model()];

        $find = $this->getMock(__NAMESPACE__.'\Find', ['execute', 'applyFlags'], [$repo]);

        $find
            ->expects($this->once())
            ->method('applyFlags')
            ->will($this->returnSelf());

        $find
            ->expects($this->once())
            ->method('execute')
            ->will($this->returnValue($models));

        $result = $find->loadRaw();

        $this->assertSame($models, $result);
    }

    /**
     * @covers ::applyFlags
     */
    public function testApplyFlags()
    {
        $repo = new Repo(__NAMESPACE__.'\Model');
        $repo->setSoftDelete(true);

        $models1 = [new Model()];
        $models2 = [new Model()];
        $models3 = [new Model()];

        $find = $this->getMock(__NAMESPACE__.'\Find', ['where', 'whereNot'], [$repo]);

        $find
            ->expects($this->at(0))
            ->method('where')
            ->with($this->equalTo('deletedAt'), $this->identicalTo(null));

        $find
            ->expects($this->at(1))
            ->method('whereNot')
            ->with($this->equalTo('deletedAt'), $this->identicalTo(null));

        $find->setFlags(State::SAVED)->applyFlags();
        $find->setFlags(State::DELETED)->applyFlags();
        $find->setFlags(State::DELETED | State::SAVED)->applyFlags();
    }

    /**
     * @covers ::setFlags
     * @covers ::getFlags
     */
    public function testFlags()
    {
        $repo = new Repo(__NAMESPACE__.'\Model');
        $repo->setSoftDelete(true);

        $find = new Find($repo);

        $this->assertSame(State::SAVED, $find->getFlags());

        $find->setFlags(State::DELETED);

        $this->assertSame(State::DELETED, $find->getFlags());

        $find->setFlags(State::DELETED | State::SAVED);

        $this->assertSame(State::DELETED | State::SAVED, $find->getFlags());

        $this->setExpectedException('InvalidArgumentException', 'Flags were 1, but need to be State::SAVED, State::DELETED or State::DELETED | State::SAVED');

        $find->setFlags(State::PENDING);
    }

    /**
     * @covers ::onlyDeleted
     * @covers ::onlySaved
     * @covers ::deletedAndSaved
     */
    public function testFlagSetters()
    {
        $repo = new Repo(__NAMESPACE__.'\Model');
        $repo->setSoftDelete(true);

        $find = new Find($repo);

        $find->onlyDeleted();

        $this->assertSame(State::DELETED, $find->getFlags());

        $find->onlySaved();

        $this->assertSame(State::SAVED, $find->getFlags());

        $find->deletedAndSaved();

        $this->assertSame(State::DELETED | State::SAVED, $find->getFlags());
    }

    /**
     * @covers ::load
     */
    public function testLoad()
    {
        $repo = Model::getRepo();

        $model1 = new Model(['id' => 10], State::SAVED);
        $model2 = new Model(['id' => 10], State::SAVED);
        $model3 = new Model(['id' => 4], State::SAVED);
        $model4 = new Model(['id' => 4], State::SAVED);

        $find = $this->getMock(__NAMESPACE__.'\Find', ['loadRaw'], [$repo]);

        $find
            ->expects($this->exactly(2))
            ->method('loadRaw')
            ->will($this->onConsecutiveCalls([$model1, $model3], [$model2, $model4]));

        $loaded = $find->load();
        $this->assertInstanceOf('Harp\Core\Model\Models', $loaded);

        $this->assertSame([$model1, $model3], $loaded->toArray());

        $loaded = $find->load();
        $this->assertInstanceOf('Harp\Core\Model\Models', $loaded);

        $this->assertSame([$model1, $model3], $loaded->toArray());
    }

    /**
     * @covers ::loadWith
     */
    public function testLoadWith()
    {
        $repo = $this->getMock(__NAMESPACE__.'\Repo', ['loadAllRelsFor'], [__NAMESPACE__.'\Model']);
        $find = $this->getMock(__NAMESPACE__.'\Find', ['load'], [$repo]);

        $rels = ['one' => 'many'];

        $models = new Models([new Model()]);

        $find
            ->expects($this->once())
            ->method('load')
            ->will($this->returnValue($models));

        $repo
            ->expects($this->once())
            ->method('loadAllRelsFor')
            ->with($this->identicalTo($models), $this->equalTo($rels), $this->equalTo(State::DELETED));

        $result = $find->setFlags(State::DELETED)->loadWith($rels);

        $this->assertSame($models, $result);
    }

    /**
     * @covers ::loadIds
     */
    public function testLoadIds()
    {
        $find = $this->getMock(__NAMESPACE__.'\Find', ['load'], [Model::getRepo()]);

        $models = new Models([
            new Model(['id' => 4]),
            new Model(['id' => 98]),
            new Model(['id' => 100]),
        ]);

        $find
            ->expects($this->once())
            ->method('load')
            ->will($this->returnValue($models));

        $expected = [4, 98, 100];

        $result = $find->loadIds();

        $this->assertSame($expected, $result);
    }

    /**
     * @covers ::loadCount
     */
    public function testLoadCount()
    {
        $find = $this->getMock(__NAMESPACE__.'\Find', ['loadRaw'], [Model::getRepo()]);

        $models = [
            new Model(['id' => 4]),
            new Model(['id' => 98]),
            new Model(['id' => 100]),
        ];

        $find
            ->expects($this->once())
            ->method('loadRaw')
            ->will($this->returnValue($models));

        $result = $find->loadCount();

        $this->assertEquals(3, $result);
    }


    /**
     * @covers ::loadFirst
     */
    public function testLoadFirst()
    {
        $repo = new Repo(__NAMESPACE__.'\Model');
        $find = $this->getMock(__NAMESPACE__.'\Find', ['limit', 'load'], [$repo]);

        $model = new Model(['id' => 300, 'repo' => $repo]);
        $models = new RepoModels($repo, [$model]);
        $emptyModels = new RepoModels($repo);

        $find
            ->expects($this->exactly(2))
            ->method('limit')
            ->with($this->equalTo(1))
            ->will($this->returnSelf());

        $find
            ->expects($this->exactly(2))
            ->method('load')
            ->will($this->onConsecutiveCalls($models, $emptyModels));

        $result = $find->loadFirst();

        $this->assertSame($model, $result);

        $result = $find->loadFirst();

        $this->assertInstanceOf(__NAMESPACE__.'\Model', $result);
        $this->assertTrue($result->isVoid());
    }
}
