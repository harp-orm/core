<?php

namespace CL\LunaCore\Test\Unit\Model;

use CL\LunaCore\Model\Models;
use CL\Util\Objects;
use SplObjectStorage;
use CL\LunaCore\Test\AbstractTestCase;

class ModelsTest extends AbstractTestCase
{
    /**
     * @covers CL\LunaCore\Model\Models::fromObjects
     */
    public function testFromObjects()
    {
        $objects = new SplObjectStorage();
        $objects->attach(new Model());
        $objects->attach(new Model());

        $models = Models::fromObjects($objects);
        $this->assertSame(Objects::toArray($objects), Objects::toArray($models->all()));
    }

    /**
     * @covers CL\LunaCore\Model\Models::__construct
     * @covers CL\LunaCore\Model\Models::all
     */
    public function testConstruct()
    {
        $source = [new Model(), new Model()];

        $models = new Models($source);

        $this->assertSame($source, Objects::toArray($models->all()));
    }

    /**
     * @covers CL\LunaCore\Model\Models::clear
     */
    public function testClear()
    {
        $models = new Models([new Model(), new Model()]);

        $this->assertCount(2, $models);

        $models->clear();

        $this->assertCount(0, $models);
    }

    /**
     * @covers CL\LunaCore\Model\Models::getFirst
     */
    public function testGetFirst()
    {
        $model1 = new Model();
        $model2 = new Model();

        $models = new Models([$model1, $model2]);

        $this->assertSame($model1, $models->getFirst());

        $models->clear();

        $this->assertNull($models->getFirst());
    }

    /**
     * @covers CL\LunaCore\Model\Models::addObjects
     */
    public function testAddObjects()
    {
        $models = new Models();
        $model1 = new Model();
        $model2 = new Model();

        $objects = new SplObjectStorage();
        $objects->attach($model1);
        $objects->attach($model2);

        $models->addObjects($objects);

        $this->assertSame([$model1, $model2], Objects::toArray($models->all()));
    }

    /**
     * @covers CL\LunaCore\Model\Models::addArray
     */
    public function testAddArray()
    {
        $models = new Models();
        $array = [new Model(), new Model()];

        $models->addArray($array);

        $this->assertSame($array, Objects::toArray($models->all()));
    }

    /**
     * @covers CL\LunaCore\Model\Models::add
     */
    public function testAdd()
    {
        $models = new Models();

        $model = new Model();

        $models->add($model);

        $this->assertSame([$model], Objects::toArray($models->all()));
    }

    /**
     * @covers CL\LunaCore\Model\Models::addAll
     */
    public function testAddAll()
    {
        $models = new Models();

        $model1 = new Model();
        $model2 = new Model();
        $model3 = new Model();

        $models->addAll(new Models());

        $this->assertEmpty($models);

        $models->addAll(new Models([$model1, $model2]));
        $models->addAll(new Models([$model1, $model3]));

        $this->assertCount(3, $models);

        $this->assertSame([$model1, $model2, $model3], Objects::toArray($models->all()));
    }


    /**
     * @covers CL\LunaCore\Model\Models::remove
     */
    public function testRemove()
    {
        $model = new Model();
        $models = new Models([$model]);

        $models->remove($model);

        $this->assertCount(0, $models);
    }

    /**
     * @covers CL\LunaCore\Model\Models::removeAll
     */
    public function testRemoveAll()
    {
        $source1 = [new Model(), new Model()];
        $source2 = array_merge([new Model()], $source1);
        $models1 = new Models($source1);
        $models2 = new Models($source2);

        $models2->removeAll($models1);

        $this->assertCount(1, $models2);
    }

    /**
     * @covers CL\LunaCore\Model\Models::filter
     */
    public function testFilter()
    {
        $source = [
            new Model(['name' => 'test1']),
            new Model(['name' => 'test1']),
            new Model(['name' => 'test2']),
        ];

        $models = new Models($source);

        $filtered = $models->filter(function($model){
            return $model->name !== 'test1';
        });

        $this->assertInstanceOf('CL\LunaCore\Model\Models', $filtered);
        $this->assertEquals([$source[2]], Objects::toArray($filtered->all()));
    }

    /**
     * @covers CL\LunaCore\Model\Models::byRepo
     */
    public function testByRepo()
    {
        $source = [
            0 => new Model(),
            1 => new Model(),
            2 => new SoftDeleteModel(),
            3 => new Model(),
            4 => new SoftDeleteModel(),
        ];

        $models = new Models($source);

        $expected = [
            [Repo::get(), [$source[0], $source[1], $source[3]]],
            [SoftDeleteRepo::get(), [$source[2], $source[4]]],
        ];

        $i = 0;

        $models->byRepo(function($repo, Models $repoModels) use ($expected, & $i) {
            $this->assertSame($expected[$i][0], $repo);
            $this->assertSame($expected[$i][1], Objects::toArray($repoModels->all()));
            $i++;
        });
    }

    /**
     * @covers CL\LunaCore\Model\Models::isEmpty
     */
    public function testIsEmpty()
    {
        $model = new Model();
        $models = new Models();

        $this->assertTrue($models->isEmpty());

        $models->add($model);

        $this->assertFalse($models->isEmpty());
    }

    /**
     * @covers CL\LunaCore\Model\Models::has
     */
    public function testHas()
    {
        $model = new Model();
        $models = new Models();

        $this->assertFalse($models->has($model));

        $models->add($model);

        $this->assertTrue($models->has($model));
    }

    /**
     * @covers CL\LunaCore\Model\Models::toArray
     */
    public function testToArray()
    {
        $source = [new Model(), new Model()];
        $models = new Models($source);

        $array = $models->toArray();

        $this->assertSame($source, $array);
    }

    /**
     * @covers CL\LunaCore\Model\Models::count
     */
    public function testCountable()
    {
        $models = new Models([new Model(), new Model()]);
        $this->assertCount(2, $models);
        $models->add(new Model());
        $this->assertCount(3, $models);
    }

    /**
     * @covers CL\LunaCore\Model\Models::pluckProperty
     */
    public function testPluckProperty()
    {
        $models = new Models([
            new Model(['id' => 10, 'name' => 'test1']),
            new Model(['id' => 20, 'name' => 'test2']
        )]);

        $expected = [10, 20];

        $this->assertSame($expected, $models->pluckProperty('id'));

        $expected = ['test1', 'test2'];

        $this->assertSame($expected, $models->pluckProperty('name'));
    }

    /**
     * @covers CL\LunaCore\Model\Models::current
     * @covers CL\LunaCore\Model\Models::key
     * @covers CL\LunaCore\Model\Models::next
     * @covers CL\LunaCore\Model\Models::rewind
     * @covers CL\LunaCore\Model\Models::valid
     */
    public function testIterator()
    {
        $source = [new Model(), new Model()];
        $models = new Models($source);

        $key = $models->key();

        foreach ($models as $i => $model) {
            $this->assertSame(current($source), $model);
            next($source);
        }
    }
}
