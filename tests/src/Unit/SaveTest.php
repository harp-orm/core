<?php

namespace CL\LunaCore\Test\Unit\Repo;

use CL\LunaCore\Repo\Save;
use CL\LunaCore\Repo\Event;
use CL\LunaCore\Repo\LinkOne;
use CL\LunaCore\Model\State;
use CL\Util\Objects;
use SplObjectStorage;

class SaveTest extends AbstractRepoTestCase
{
    /**
     * @covers CL\LunaCore\Repo\Save::__construct
     * @covers CL\LunaCore\Repo\Save::all
     */
    public function testConstruct()
    {
        $save = new Save();

        $this->assertInstanceOf('SplObjectStorage', $save->all());
        $this->assertCount(0, $save->all());
    }

    /**
     * @covers CL\LunaCore\Repo\Save::addModel
     */
    public function testAddModel()
    {
        $save = new Save();
        $model = new Model();

        $save->addModel($model);

        $this->assertCount(1, $save->all());
        $this->assertTrue($save->all()->contains($model));
    }

    /**
     * @covers CL\LunaCore\Repo\Save::getChanged
     * @covers CL\LunaCore\Repo\Save::getPending
     * @covers CL\LunaCore\Repo\Save::getDeleted
     */
    public function testFilters()
    {
        $save = new Save();

        $model1 = new Model();
        $model1->setState(State::SAVED);
        $model1->name = 'changed';
        $save->all()->attach($model1);

        $model2 = new Model();
        $model2->setState(State::VOID);
        $save->all()->attach($model2);

        $model3 = new Model();
        $model3->setState(State::PENDING);
        $save->all()->attach($model3);

        $model4 = new Model();
        $model4->setState(State::DELETED);
        $save->all()->attach($model4);

        $model5 = new Model();
        $model5->setState(State::SAVED);
        $save->all()->attach($model5);

        $this->assertSame([$model1], Objects::toArray($save->getChanged()));
        $this->assertSame([$model3], Objects::toArray($save->getPending()));
        $this->assertSame([$model4], Objects::toArray($save->getDeleted()));
    }

    /**
     * @covers CL\LunaCore\Repo\Save::add
     */
    public function testAdd()
    {
        $save = new Save();

        $model1 = new Model();
        $model2 = new Model();
        $model3 = new Model();
        $model4 = new Model();

        $link1 = $this->getLinkOne();
        $link1->set($model2);
        $link2 = $this->getLinkMany();
        $link2->remove($link2->getFirst());
        $link2->add($model3);
        $link2->add($model4);

        Repo::get()
            ->setRels([
                $link1->getRel(),
                $link2->getRel(),
            ])
            ->addLink($model1, $link1)
            ->addLink($model2, $link2);

        $save->add($model1);

        $this->assertCount(7, $save->all());

        $link2Models = Objects::toArray($link2->getCurrentAndOriginal());

        $expected = [
            $model1,
            $link1->getOriginal(),
            $model2,
            $link2Models[0],
            $link2Models[1],
            $link2Models[2],
            $link2Models[3],
        ];

        foreach ($expected as $model) {
            $this->assertContains($model, $save->all());
        }
    }

    /**
     * @covers CL\LunaCore\Repo\Save::set
     */
    public function testSet()
    {
        $save = $this->getMock('CL\LunaCore\Repo\Save', ['add']);

        $models = [new Model(), new Model(), new Model()];

        foreach ($models as $index => $model) {
            $save
                ->expects($this->at($index))
                ->method('add')
                ->with($this->identicalTo($model));
        }

        $save->set($models);
    }

    /**
     * @covers CL\LunaCore\Repo\Save::eachLink
     */
    public function testEachLink()
    {
        $save = new Save();

        $model1 = new Model();
        $model2 = new Model();

        $link1 = $this->getLinkOne();
        $link1->set($model2);
        $link2 = $this->getLinkMany();

        Repo::get()
            ->setRels([
                $link1->getRel(),
                $link2->getRel(),
            ])
            ->addLink($model1, $link1)
            ->addLink($model2, $link2);

        $save->add($model1);

        $results = [];

        $save->eachLink(function ($model, $link) use (& $results) {
            $results []= [$model, $link];
        });

        $expected = [
            [$model1, $link1],
            [$model2, $link2]
        ];

        $this->assertSame($expected, $results);
    }

    /**
     * @covers CL\LunaCore\Repo\Save::addFromDeleteRels
     */
    public function testAddFromDeleteRels()
    {
        $save = new Save();

        $model1 = new Model();
        $model2 = new Model();
        $model3 = new Model();
        $model4 = new Model();
        $model5 = new Model();

        $rel1 = $this->getMock(
            __NAMESPACE__.'\RelOneDelete',
            ['delete'],
            ['test', Repo::get(), Repo::get()]
        );

        $rel2 = $this->getRelOne();

        $link1 = new LinkOne($rel1, $model3);
        $link2 = new LinkOne($rel2, $model4);

        Repo::get()
            ->setRels([$rel1, $rel2])
            ->addLink($model1, $link1)
            ->addLink($model2, $link2);

        $save->add($model1);

        $rel1
            ->expects($this->once())
            ->method('delete')
            ->with($this->identicalTo($model1), $this->identicalTo($link1))
            ->will($this->returnValue([$model5]));

        $this->assertFalse($save->all()->contains($model5));

        $save->addFromDeleteRels();

        $this->assertTrue($save->all()->contains($model5));
    }

    /**
     * @covers CL\LunaCore\Repo\Save::addFromInsertRels
     */
    public function testAddFromInsertRels()
    {
        $save = new Save();

        $model1 = new Model();
        $model2 = new Model();
        $model3 = new Model();
        $model4 = new Model();
        $model5 = new Model();

        $rel1 = $this->getMock(
            __NAMESPACE__.'\RelOneInsert',
            ['insert'],
            ['test', Repo::get(), Repo::get()]
        );

        $rel2 = $this->getRelOne();

        $link1 = new LinkOne($rel1, $model3);
        $link2 = new LinkOne($rel2, $model4);

        Repo::get()
            ->setRels([$rel1, $rel2])
            ->addLink($model1, $link1)
            ->addLink($model2, $link2);

        $save->add($model1);

        $rel1
            ->expects($this->once())
            ->method('insert')
            ->with($this->identicalTo($model1), $this->identicalTo($link1))
            ->will($this->returnValue([$model5]));

        $this->assertFalse($save->all()->contains($model5));

        $save->addFromInsertRels();

        $this->assertTrue($save->all()->contains($model5));
    }


    /**
     * @covers CL\LunaCore\Repo\Save::callUpdateRels
     */
    public function testCallUpdateRels()
    {
        $save = new Save();

        $model1 = new Model();
        $model2 = new Model();
        $model3 = new Model();
        $model4 = new Model();

        $rel1 = $this->getMock(
            __NAMESPACE__.'\RelOneUpdate',
            ['update'],
            ['test', Repo::get(), Repo::get()]
        );

        $rel2 = $this->getRelOne();

        $link1 = new LinkOne($rel1, $model3);
        $link2 = new LinkOne($rel2, $model4);

        Repo::get()
            ->setRels([$rel1, $rel2])
            ->addLink($model1, $link1)
            ->addLink($model2, $link2);

        $save->add($model1);

        $rel1
            ->expects($this->once())
            ->method('update')
            ->with($this->identicalTo($model1), $this->identicalTo($link1));

        $save->callUpdateRels();
    }

    /**
     * @covers CL\LunaCore\Repo\Save::groupByRepo
     */
    public function testGroupByRepo()
    {
        $save = new Save();

        $repo1 = new Repo(__NAMESPACE__.'\ModelWithRepo');
        $repo2 = new Repo(__NAMESPACE__.'\ModelWithRepo');

        $model1 = new ModelWithRepo(['repo' => $repo1]);
        $model2 = new ModelWithRepo(['repo' => $repo2]);
        $model3 = new ModelWithRepo(['repo' => $repo2]);

        $models = new SplObjectStorage();
        $models->attach($model1);
        $models->attach($model2);
        $models->attach($model3);

        $result = Persist::groupByRepo($models);

        $this->assertCount(2, $result);
        $this->assertTrue($result->contains($repo1));
        $this->assertTrue($result->contains($repo2));

        $result1 = $result[$repo1];
        $this->assertCount(1, $result1);
        $this->assertTrue($result1->contains($model1));

        $result2 = $result[$repo2];
        $this->assertCount(2, $result2);
        $this->assertTrue($result2->contains($model2));
        $this->assertTrue($result2->contains($model3));
    }

    /**
     * @covers CL\LunaCore\Repo\Save::execute
     * @covers CL\LunaCore\Repo\Save::save
     */
    public function testExecute()
    {
        $save = new Save();

        $repo1 = $this->getMock(
            __NAMESPACE__.'\Repo',
            ['delete', 'dispatchBeforeEvent', 'dispatchAfterEvent'],
            [__NAMESPACE__.'\ModelWithRepo']
        );

        $repo2 = $this->getMock(
            __NAMESPACE__.'\Repo',
            ['update', 'insert', 'dispatchBeforeEvent', 'dispatchAfterEvent'],
            [__NAMESPACE__.'\ModelWithRepo']
        );

        $model1 = new ModelWithRepo(['repo' => $repo1], State::DELETED);

        $model2 = new ModelWithRepo(['repo' => $repo2], State::PENDING);

        $model3 = new ModelWithRepo(['repo' => $repo2], State::SAVED);
        $model3->name = 'changed';

        $models1 = new SplObjectStorage();
        $models1->attach($model1);

        $models2 = new SplObjectStorage();
        $models2->attach($model2);

        $models3 = new SplObjectStorage();
        $models3->attach($model3);

        $save = new Save();
        $save->all()->attach($model1);
        $save->all()->attach($model2);
        $save->all()->attach($model3);

        // DELETE
        $repo1
            ->expects($this->at(0))
            ->method('dispatchBeforeEvent')
            ->with($this->equalTo($models1), $this->equalTo(Event::DELETE));

        $repo1
            ->expects($this->at(1))
            ->method('delete')
            ->with($this->equalTo($models1));

        $repo1
            ->expects($this->at(2))
            ->method('dispatchAfterEvent')
            ->with($this->equalTo($models1), $this->equalTo(Event::DELETE));

        // INSERT
        $repo2
            ->expects($this->at(0))
            ->method('dispatchBeforeEvent')
            ->with($this->equalTo($models2), $this->equalTo(Event::INSERT));

        $repo2
            ->expects($this->at(1))
            ->method('dispatchBeforeEvent')
            ->with($this->equalTo($models2), $this->equalTo(Event::SAVE));

        $repo2
            ->expects($this->at(2))
            ->method('insert')
            ->with($this->equalTo($models2));

        $repo2
            ->expects($this->at(3))
            ->method('dispatchAfterEvent')
            ->with($this->equalTo($models2), $this->equalTo(Event::INSERT));

        $repo2
            ->expects($this->at(4))
            ->method('dispatchAfterEvent')
            ->with($this->equalTo($models2), $this->equalTo(Event::SAVE));

        // UPDATE
        $repo2
            ->expects($this->at(5))
            ->method('dispatchBeforeEvent')
            ->with($this->equalTo($models3), $this->equalTo(Event::UPDATE));

        $repo2
            ->expects($this->at(6))
            ->method('dispatchBeforeEvent')
            ->with($this->equalTo($models3), $this->equalTo(Event::SAVE));

        $repo2
            ->expects($this->at(7))
            ->method('update')
            ->with($this->equalTo($models3));

        $repo2
            ->expects($this->at(8))
            ->method('dispatchAfterEvent')
            ->with($this->equalTo($models3), $this->equalTo(Event::UPDATE));

        $repo2
            ->expects($this->at(9))
            ->method('dispatchAfterEvent')
            ->with($this->equalTo($models3), $this->equalTo(Event::SAVE));

        $save->execute();
    }
}
