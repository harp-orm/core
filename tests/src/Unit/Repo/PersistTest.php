<?php

namespace CL\LunaCore\Test\Unit\Repo;

use CL\LunaCore\Repo\Persist;
use CL\LunaCore\Repo\ModelEvent;
use CL\LunaCore\Repo\LinkOne;
use CL\LunaCore\Model\AbstractModel;
use CL\LunaCore\Util\Objects;
use SplObjectStorage;

class PersistTest extends AbstractRepoTestCase
{
    /**
     * @covers CL\LunaCore\Repo\Persist::__construct
     * @covers CL\LunaCore\Repo\Persist::all
     */
    public function testConstruct()
    {
        $persist = new Persist();

        $this->assertInstanceOf('SplObjectStorage', $persist->all());
        $this->assertCount(0, $persist->all());
    }

    /**
     * @covers CL\LunaCore\Repo\Persist::addModel
     */
    public function testAddModel()
    {
        $persist = new Persist();
        $model = new Model();

        $persist->addModel($model);

        $this->assertCount(1, $persist->all());
        $this->assertTrue($persist->all()->contains($model));
    }

    /**
     * @covers CL\LunaCore\Repo\Persist::getChanged
     * @covers CL\LunaCore\Repo\Persist::getPending
     * @covers CL\LunaCore\Repo\Persist::getDeleted
     */
    public function testFilters()
    {
        $persist = new Persist();

        $model1 = new Model();
        $model1->setState(AbstractModel::PERSISTED);
        $model1->name = 'changed';
        $persist->all()->attach($model1);

        $model2 = new Model();
        $model2->setState(AbstractModel::VOID);
        $persist->all()->attach($model2);

        $model3 = new Model();
        $model3->setState(AbstractModel::PENDING);
        $persist->all()->attach($model3);

        $model4 = new Model();
        $model4->setState(AbstractModel::DELETED);
        $persist->all()->attach($model4);

        $model5 = new Model();
        $model5->setState(AbstractModel::PERSISTED);
        $persist->all()->attach($model5);

        $this->assertSame([$model1], Objects::toArray($persist->getChanged()));
        $this->assertSame([$model3], Objects::toArray($persist->getPending()));
        $this->assertSame([$model4], Objects::toArray($persist->getDeleted()));
    }

    /**
     * @covers CL\LunaCore\Repo\Persist::add
     */
    public function testAdd()
    {
        $persist = new Persist();

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

        $persist->add($model1);

        $this->assertCount(7, $persist->all());

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
            $this->assertContains($model, $persist->all());
        }
    }

    /**
     * @covers CL\LunaCore\Repo\Persist::set
     */
    public function testSet()
    {
        $persist = $this->getMock('CL\LunaCore\Repo\Persist', ['add']);

        $models = [new Model(), new Model(), new Model()];

        foreach ($models as $index => $model) {
            $persist
                ->expects($this->at($index))
                ->method('add')
                ->with($this->identicalTo($model));
        }

        $persist->set($models);
    }

    /**
     * @covers CL\LunaCore\Repo\Persist::eachLink
     */
    public function testEachLink()
    {
        $persist = new Persist();

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

        $persist->add($model1);

        $results = [];

        $persist->eachLink(function ($model, $link) use (& $results) {
            $results []= [$model, $link];
        });

        $expected = [
            [$model1, $link1],
            [$model2, $link2]
        ];

        $this->assertSame($expected, $results);
    }

    /**
     * @covers CL\LunaCore\Repo\Persist::addFromDeleteRels
     */
    public function testAddFromDeleteRels()
    {
        $persist = new Persist();

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

        $persist->add($model1);

        $rel1
            ->expects($this->once())
            ->method('delete')
            ->with($this->identicalTo($model1), $this->identicalTo($link1))
            ->will($this->returnValue([$model5]));

        $this->assertFalse($persist->all()->contains($model5));

        $persist->addFromDeleteRels();

        $this->assertTrue($persist->all()->contains($model5));
    }

    /**
     * @covers CL\LunaCore\Repo\Persist::addFromInsertRels
     */
    public function testAddFromInsertRels()
    {
        $persist = new Persist();

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

        $persist->add($model1);

        $rel1
            ->expects($this->once())
            ->method('insert')
            ->with($this->identicalTo($model1), $this->identicalTo($link1))
            ->will($this->returnValue([$model5]));

        $this->assertFalse($persist->all()->contains($model5));

        $persist->addFromInsertRels();

        $this->assertTrue($persist->all()->contains($model5));
    }


    /**
     * @covers CL\LunaCore\Repo\Persist::callUpdateRels
     */
    public function testCallUpdateRels()
    {
        $persist = new Persist();

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

        $persist->add($model1);

        $rel1
            ->expects($this->once())
            ->method('update')
            ->with($this->identicalTo($model1), $this->identicalTo($link1));

        $persist->callUpdateRels();
    }

    /**
     * @covers CL\LunaCore\Repo\Persist::groupByRepo
     */
    public function testGroupByRepo()
    {
        $persist = new Persist();

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
     * @covers CL\LunaCore\Repo\Persist::execute
     * @covers CL\LunaCore\Repo\Persist::persist
     */
    public function testExecute()
    {
        $persist = new Persist();

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

        $model1 = new ModelWithRepo(['repo' => $repo1], AbstractModel::DELETED);

        $model2 = new ModelWithRepo(['repo' => $repo2], AbstractModel::PENDING);

        $model3 = new ModelWithRepo(['repo' => $repo2], AbstractModel::PERSISTED);
        $model3->name = 'changed';

        $models1 = new SplObjectStorage();
        $models1->attach($model1);

        $models2 = new SplObjectStorage();
        $models2->attach($model2);

        $models3 = new SplObjectStorage();
        $models3->attach($model3);

        $persist = new Persist();
        $persist->all()->attach($model1);
        $persist->all()->attach($model2);
        $persist->all()->attach($model3);

        // DELETE
        $repo1
            ->expects($this->at(0))
            ->method('dispatchBeforeEvent')
            ->with($this->equalTo($models1), $this->equalTo(ModelEvent::DELETE));

        $repo1
            ->expects($this->at(1))
            ->method('delete')
            ->with($this->equalTo($models1));

        $repo1
            ->expects($this->at(2))
            ->method('dispatchAfterEvent')
            ->with($this->equalTo($models1), $this->equalTo(ModelEvent::DELETE));

        // INSERT
        $repo2
            ->expects($this->at(0))
            ->method('dispatchBeforeEvent')
            ->with($this->equalTo($models2), $this->equalTo(ModelEvent::INSERT));

        $repo2
            ->expects($this->at(1))
            ->method('dispatchBeforeEvent')
            ->with($this->equalTo($models2), $this->equalTo(ModelEvent::SAVE));

        $repo2
            ->expects($this->at(2))
            ->method('insert')
            ->with($this->equalTo($models2));

        $repo2
            ->expects($this->at(3))
            ->method('dispatchAfterEvent')
            ->with($this->equalTo($models2), $this->equalTo(ModelEvent::INSERT));

        $repo2
            ->expects($this->at(4))
            ->method('dispatchAfterEvent')
            ->with($this->equalTo($models2), $this->equalTo(ModelEvent::SAVE));

        // UPDATE
        $repo2
            ->expects($this->at(5))
            ->method('dispatchBeforeEvent')
            ->with($this->equalTo($models3), $this->equalTo(ModelEvent::UPDATE));

        $repo2
            ->expects($this->at(6))
            ->method('dispatchBeforeEvent')
            ->with($this->equalTo($models3), $this->equalTo(ModelEvent::SAVE));

        $repo2
            ->expects($this->at(7))
            ->method('update')
            ->with($this->equalTo($models3));

        $repo2
            ->expects($this->at(8))
            ->method('dispatchAfterEvent')
            ->with($this->equalTo($models3), $this->equalTo(ModelEvent::UPDATE));

        $repo2
            ->expects($this->at(9))
            ->method('dispatchAfterEvent')
            ->with($this->equalTo($models3), $this->equalTo(ModelEvent::SAVE));

        $persist->execute();
    }
}
