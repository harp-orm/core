<?php

namespace CL\LunaCore\Test\Unit\Save;

use CL\LunaCore\Save\Save;
use CL\LunaCore\Repo\Event;
use CL\LunaCore\Repo\LinkOne;
use CL\LunaCore\Repo\LinkMany;
use CL\LunaCore\Repo\AbstractLink;
use CL\LunaCore\Model\State;
use CL\LunaCore\Model\Models;
use CL\LunaCore\Test\AbstractTestCase;
use CL\Util\Objects;
use SplObjectStorage;

class SaveTest extends AbstractTestCase
{
    /**
     * @covers CL\LunaCore\Save\Save::fromObjects
     */
    public function testFromObjects()
    {
        $objects = new SplObjectStorage();
        $objects->attach(new Model());
        $objects->attach(new Model());

        $save = Save::fromObjects($objects);
        $this->assertSame(Objects::toArray($objects), Objects::toArray($save->all()));
    }

    /**
     * @covers CL\LunaCore\Save\Save::addModel
     */
    public function testAddModel()
    {
        $save = new Save();
        $model = new Model();

        $save->addModel($model);

        $this->assertCount(1, $save);
        $this->assertTrue($save->has($model));
    }

    public function dataFilters()
    {
        $models = [
            1 => new Model(null, State::SAVED),
            2 => new Model(null, State::VOID),
            3 => new Model(null, State::PENDING),
            4 => new Model(null, State::DELETED),
            5 => new SoftDeleteModel(['deletedAt' => time()], State::DELETED),
            6 => new SoftDeleteModel(null, State::SAVED),
            7 => new SoftDeleteModel(null, State::PENDING),
            8 => new SoftDeleteModel(null, State::DELETED),
        ];

        return [
            [$models, 'getModelsToDelete', [$models[4], $models[8]]],
        ];
    }

    /**
     * @dataProvider dataFilters
     * @covers CL\LunaCore\Save\Save::getModelsToDelete
     * @covers CL\LunaCore\Save\Save::getModelsToInsert
     * @covers CL\LunaCore\Save\Save::getModelsToUpdate
     */
    public function testFilters($models, $filter, $expected)
    {
        $save = new Save();

        $save->addArray($models);

        $filtered = $save->$filter();

        $this->assertInstanceOf('CL\LunaCore\Model\Models', $filtered);
        $this->assertSame($expected, $filtered->toArray());
    }

    /**
     * @covers CL\LunaCore\Save\Save::add
     */
    public function testAdd()
    {
        $save = new Save();

        $models = [
            new Model(),
            new Model(),
            new Model(),
            new Model(),
            new Model(),
            new Model(),
        ];

        $link1 = new LinkOne(Repo::get()->getRel('one'), $models[1]);
        $link1->set($models[5]);
        $link2 = new LinkMany(Repo::get()->getRel('many'), [$models[2], $models[3]]);
        $link2->remove($models[3]);
        $link2->add($models[4]);

        Repo::get()
            ->addLink($models[0], $link1)
            ->addLink($models[1], $link2);

        $save->add($models[0]);

        $this->assertCount(count($models), $save);

        foreach ($models as $model) {
            $this->assertTrue($save->has($model));
        }
    }

    /**
     * @covers CL\LunaCore\Save\Save::eachLink
     */
    public function testEachLink()
    {
        $save = new Save();

        $model1 = new Model();
        $model2 = new Model();

        $link1 = new LinkOne(Repo::get()->getRel('one'), $model2);
        $link2 = new LinkMany(Repo::get()->getRel('many'), []);

        Repo::get()
            ->addLink($model1, $link1)
            ->addLink($model2, $link2);

        $save->add($model1);

        $i = 0;

        $expected = [
            [$model1, $link1],
            [$model2, $link2]
        ];

        $save->eachLink(function(Model $model, AbstractLink $link) use ($expected, & $i) {
            $this->assertSame($expected[$i][0], $model);
            $this->assertSame($expected[$i][1], $link);
            $i++;
        });
    }

    public function dataRelModifiers()
    {
        return [
            ['delete', 'addFromDeleteRels', true],
            ['insert', 'addFromInsertRels', true],
            ['update', 'callUpdateRels', false],
        ];
    }

    /**
     * @dataProvider dataRelModifiers
     * @covers CL\LunaCore\Save\Save::addFromDeleteRels
     * @covers CL\LunaCore\Save\Save::addFromInsertRels
     * @covers CL\LunaCore\Save\Save::callUpdateRels
     */
    public function testRelModifiers($method, $trigger, $expectAdd)
    {
        $save = new Save();

        $model1 = new Model();
        $model2 = new Model();
        $model3 = new Model();
        $model4 = new Model();
        $model5 = new Model();

        $rel1 = $this->getMock(
            __NAMESPACE__.'\RelOne',
            [$method],
            ['test', Repo::get(), Repo::get()]
        );
        $rel2 = Repo::get()->getRel('one');

        $link1 = new LinkOne($rel1, $model3);
        $link2 = new LinkOne($rel2, $model4);

        Repo::get()
            ->addRels([$rel1])
            ->addLink($model1, $link1)
            ->addLink($model2, $link2);

        $save->add($model1);

        $rel1
            ->expects($this->once())
            ->method($method)
            ->with($this->identicalTo($model1), $this->identicalTo($link1))
            ->will($this->returnValue(new Models([$model5])));

        $this->assertFalse($save->has($model5));

        $save->$trigger();

        if ($expectAdd) {
            $this->assertTrue($save->has($model5));
        }
    }

    /**
     * @covers CL\LunaCore\Save\Save::execute
     */
    public function testExecute()
    {
        $save = new Save();

        $repo1 = $this->getMock(
            __NAMESPACE__.'\Repo',
            ['deleteModels', 'insertModels', 'updateModels'],
            [__NAMESPACE__.'\Model']
        );

        $repo2 = $this->getMock(
            __NAMESPACE__.'\SoftDeleteRepo',
            ['deleteModels', 'insertModels', 'updateModels'],
            [__NAMESPACE__.'\SoftDeleteModel']
        );

        Repo::set($repo1);
        SoftDeleteRepo::set($repo2);

        $models = [
            1 => (new Model(['id' => 1], State::SAVED))->setProperties(['name' => 'changed']),
            2 => new Model(['id' => 2], State::VOID),
            3 => new Model(['id' => 3], State::PENDING),
            4 => new Model(['id' => 4], State::DELETED),
            5 => (new SoftDeleteModel(['id' => 5], State::DELETED))->setProperties(['deletedAt' => time()]),
            6 => new SoftDeleteModel(['id' => 6], State::SAVED),
            7 => new SoftDeleteModel(['id' => 7], State::PENDING),
            8 => new SoftDeleteModel(['id' => 8], State::DELETED),
            9 => (new SoftDeleteModel(['id' => 9], State::SAVED))->setProperties(['name' => 'changed']),
        ];

        $save = new Save();
        $save->addArray($models);

        $expected = [
            'deleteModels' => [$models[4]],
            'insertModels' => [$models[3]],
            'updateModels' => [$models[1]],
        ];

        foreach ($expected as $method => $values) {
            $repo1
                ->expects($this->once())
                ->method($method)
                ->with($this->callback(function(Models $models) use ($values) {
                    $this->assertSame($values, $models->toArray());

                    return true;
                }));
        }

        $expected = [
            'deleteModels' => [$models[8]],
            'insertModels' => [$models[7]],
            'updateModels' => [$models[5], $models[9]],
        ];

        foreach ($expected as $method => $values) {
            $repo2
                ->expects($this->once())
                ->method($method)
                ->with($this->callback(function(Models $models) use ($values) {
                    $this->assertSame($values, $models->toArray());

                    return true;
                }));
        }

        $save->execute();

        Repo::clear();
        SoftDeleteRepo::clear();
    }
}
