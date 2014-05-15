<?php

namespace CL\LunaCore\Test\Unit\Repo;

use CL\LunaCore\Repo\IdentityMap;
use CL\LunaCore\Model\AbstractModel;

class IdentityMapTest extends AbstractRepoTestCase
{
    /**
     * @covers CL\LunaCore\Repo\IdentityMap::__construct
     * @covers CL\LunaCore\Repo\IdentityMap::getRepo
     * @covers CL\LunaCore\Repo\IdentityMap::getModels
     */
    public function testConstruct()
    {
        $repo = new Repo(__NAMESPACE__.'\Model');
        $map = new IdentityMap($repo);

        $this->assertSame($repo, $map->getRepo());
        $this->assertSame([], $map->getModels());
    }

    /**
     * @covers CL\LunaCore\Repo\IdentityMap::get
     */
    public function testGet()
    {
        $map = Repo::get()->getIdentityMap()->clear();

        $model1 = new Model(['id' => 1], AbstractModel::PERSISTED);
        $model2 = new Model(['id' => 1], AbstractModel::PERSISTED);
        $model3 = new Model(['id' => 2], AbstractModel::PERSISTED);

        $this->assertSame($model1, $map->get($model1));
        $this->assertSame($model1, $map->get($model2));
        $this->assertSame($model3, $map->get($model3));
    }

    /**
     * @covers CL\LunaCore\Repo\IdentityMap::getArray
     */
    public function testGetArray()
    {
        $map = Repo::get()->getIdentityMap()->clear();

        $model1 = new Model(['id' => 1], AbstractModel::PERSISTED);
        $model2 = new Model(['id' => 2], AbstractModel::PERSISTED);

        $map->get($model1);
        $map->get($model2);

        $models = [
            new Model(['id' => 1], AbstractModel::PERSISTED),
            new Model(['id' => 2], AbstractModel::PERSISTED),
        ];

        $expected = [$model1, $model2];

        $this->assertSame($expected, $map->getArray($models));
    }

    /**
     * @covers CL\LunaCore\Repo\IdentityMap::clear
     */
    public function testClear()
    {
        $map = Repo::get()->getIdentityMap()->clear();

        $map->get(new Model(['id' => 1], AbstractModel::PERSISTED));
        $this->assertCount(1, $map->getModels());
        $map->clear();
        $this->assertCount(0, $map->getModels());
    }
}
