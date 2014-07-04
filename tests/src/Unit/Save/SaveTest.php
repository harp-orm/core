<?php

namespace Harp\Core\Test\Unit\Save;

use Harp\Core\Save\Save;
use Harp\Core\Repo\Event;
use Harp\Core\Repo\LinkOne;
use Harp\Core\Repo\LinkMany;
use Harp\Core\Repo\AbstractLink;
use Harp\Core\Model\State;
use Harp\Core\Model\Models;
use Harp\Core\Test\AbstractTestCase;
use Harp\Util\Objects;
use SplObjectStorage;

/**
 * @coversDefaultClass Harp\Core\Save\Save
 *
 * @author     Ivan Kerin <ikerin@gmail.com>
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://spdx.org/licenses/BSD-3-Clause
 */
class SaveTest extends AbstractTestCase
{
    /**
     * @covers ::__construct
     */
    public function testConstruct()
    {
        $model1 = new Model();
        $model2 = new Model();
        $save = new Save([$model1, $model2]);

        $this->assertCount(2, $save);
        $this->assertTrue($save->has($model1));
        $this->assertTrue($save->has($model2));
    }

    /**
     * @covers ::addShallow
     */
    public function testAddShallow()
    {
        $save = new Save();
        $model = new Model();

        $save->addShallow($model);

        $this->assertCount(1, $save);
        $this->assertTrue($save->has($model));
    }

    public function dataFilters()
    {
        $models = [
            1 => (new Model(null, State::SAVED))->setProperties(['name' => '1233']),
            2 => new Model(null, State::VOID),
            3 => new Model(null, State::PENDING),
            4 => new Model(null, State::DELETED),
            5 => (new SoftDeleteModel(null, State::SAVED))->delete(),
            6 => new SoftDeleteModel(null, State::SAVED),
            7 => new SoftDeleteModel(null, State::PENDING),
            8 => new SoftDeleteModel(null, State::DELETED),
        ];

        return [
            [$models, 'getModelsToDelete', [$models[4], $models[8]]],
            [$models, 'getModelsToInsert', [$models[3], $models[7]]],
            [$models, 'getModelsToUpdate', [$models[1], $models[5]]],
        ];
    }

    /**
     * @dataProvider dataFilters
     * @covers ::getModelsToDelete
     * @covers ::getModelsToInsert
     * @covers ::getModelsToUpdate
     */
    public function testFilters($models, $filter, $expected)
    {
        $save = new Save();

        $save->addArray($models);

        $filtered = $save->$filter();

        $this->assertInstanceOf('Harp\Core\Model\Models', $filtered);
        $this->assertSame($expected, $filtered->toArray());
    }

    /**
     * @covers ::add
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

        $link1 = new LinkOne($models[0], Model::getRepo()->getRel('one'), $models[1]);
        $link1->set($models[5]);
        $link2 = new LinkMany($models[1], Model::getRepo()->getRel('many'), [$models[2], $models[3]]);
        $link2->remove($models[3]);
        $link2->add($models[4]);

        Model::getRepo()
            ->addLink($link1)
            ->addLink($link2);

        $save->add($models[0]);

        $this->assertCount(count($models), $save);

        foreach ($models as $model) {
            $this->assertTrue($save->has($model));
        }
    }

    /**
     * @covers ::addArray
     */
    public function testAddArray()
    {
        $save = $this->getMock('Harp\Core\Save\Save', ['add']);

        $model1 = new Model();
        $model2 = new Model();

        $save
            ->expects($this->at(0))
            ->method('add')
            ->with($this->identicalTo($model1));

        $save
            ->expects($this->at(1))
            ->method('add')
            ->with($this->identicalTo($model2));

        $save->addArray([$model1, $model2]);
    }

    /**
     * @covers ::addAll
     */
    public function testAddAll()
    {
        $save = $this->getMock('Harp\Core\Save\Save', ['add']);

        $model1 = new Model();
        $model2 = new Model();

        $save
            ->expects($this->at(0))
            ->method('add')
            ->with($this->identicalTo($model1));

        $save
            ->expects($this->at(1))
            ->method('add')
            ->with($this->identicalTo($model2));

        $save->addAll(new Models([$model1, $model2]));
    }

    /**
     * @covers ::has
     * @covers ::count
     * @covers ::clear
     */
    public function testInterface()
    {
        $save = new Save();
        $model = new Model();

        $this->assertFalse($save->has($model));
        $save->add($model);
        $this->assertTrue($save->has($model));

        $this->assertCount(1, $save);
        $save->clear();
        $this->assertCount(0, $save);
    }

    /**
     * @covers ::eachLink
     */
    public function testEachLink()
    {
        $save = new Save();

        $model1 = new Model();
        $model2 = new Model();
        $model3 = new Model();

        $link1 = new LinkOne($model1, Model::getRepo()->getRel('one'), $model2);
        $link2 = new LinkMany($model2, Model::getRepo()->getRel('many'), []);

        Model::getRepo()
            ->addLink($link1)
            ->addLink($link2);

        $save->add($model1);

        $i = 0;

        $expected = [
            [$model1, $link1],
            [$model2, $link2]
        ];

        $save->eachLink(function(AbstractLink $link) use ($expected, $model3, & $i) {
            $this->assertSame($expected[$i][0], $link->getModel());
            $this->assertSame($expected[$i][1], $link);
            $i++;

            return new Models([$model3]);
        });

        $this->assertTrue($save->has($model3));
    }

    public function dataRelModifiers()
    {
        return [
            ['delete', 'addFromDeleteRels'],
            ['insert', 'addFromInsertRels'],
            ['update', 'addFromUpdateRels'],
        ];
    }

    /**
     * @dataProvider dataRelModifiers
     * @covers ::addFromDeleteRels
     * @covers ::addFromInsertRels
     * @covers ::addFromUpdateRels
     */
    public function testRelModifiers($method, $trigger)
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
            ['test', Model::getRepo(), Model::getRepo()]
        );
        $rel2 = Model::getRepo()->getRel('one');

        $link1 = new LinkOne($model1, $rel1, $model3);
        $link2 = new LinkOne($model2, $rel2, $model4);

        Model::getRepo()
            ->addRels([$rel1])
            ->addLink($link1)
            ->addLink($link2);

        $save->add($model1);

        $rel1
            ->expects($this->once())
            ->method($method)
            ->with($this->identicalTo($link1))
            ->will($this->returnValue(new Models([$model5])));

        $this->assertFalse($save->has($model5));

        $save->$trigger();

        $this->assertTrue($save->has($model5));
    }

    /**
     * @covers ::execute
     */
    public function testExecute()
    {
        $save = new Save();

        $repo1 = $this->getMock(
            __NAMESPACE__.'\Repo',
            ['deleteModels', 'insertModels', 'updateModels', 'get'],
            [__NAMESPACE__.'\Model']
        );

        $repo1
            ->expects($this->any())
            ->method('get')
            ->will($this->returnValue($repo1));

        $repo2 = $this->getMock(
            __NAMESPACE__.'\Repo',
            ['deleteModels', 'insertModels', 'updateModels', 'get'],
            [__NAMESPACE__.'\SoftDeleteModel']
        );

        $repo2
            ->expects($this->any())
            ->method('get')
            ->will($this->returnValue($repo2));

        Model::$repo = $repo1;
        SoftDeleteModel::$repo = $repo2;

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
    }
}
