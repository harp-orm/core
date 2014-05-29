<?php

namespace Harp\Core\Test\Unit\Repo;

use Harp\Core\Repo\IdentityMap;
use Harp\Core\Model\State;

/**
 * @coversDefaultClass Harp\Core\Repo\IdentityMap
 */
class IdentityMapTest extends AbstractRepoTestCase
{
    /**
     * @covers ::__construct
     * @covers ::getRepo
     * @covers ::getModels
     */
    public function testConstruct()
    {
        $repo = new Repo(__NAMESPACE__.'\Model');
        $map = new IdentityMap($repo);

        $this->assertSame($repo, $map->getRepo());
        $this->assertSame([], $map->getModels());
    }

    /**
     * @covers ::get
     */
    public function testGet()
    {
        $map = Repo::get()->getIdentityMap()->clear();

        $model1 = new Model(['id' => 1], State::SAVED);
        $model2 = new Model(['id' => 1], State::SAVED);
        $model3 = new Model(['id' => 2], State::SAVED);

        $this->assertSame($model1, $map->get($model1));
        $this->assertSame($model1, $map->get($model2));
        $this->assertSame($model3, $map->get($model3));
    }

    /**
     * @covers ::getArray
     */
    public function testGetArray()
    {
        $map = Repo::get()->getIdentityMap()->clear();

        $model1 = new Model(['id' => 1], State::SAVED);
        $model2 = new Model(['id' => 2], State::SAVED);

        $map->get($model1);
        $map->get($model2);

        $models = [
            new Model(['id' => 1], State::SAVED),
            new Model(['id' => 2], State::SAVED),
        ];

        $expected = [$model1, $model2];

        $this->assertSame($expected, $map->getArray($models));
    }

    /**
     * @covers ::has
     */
    public function testHas()
    {
        $map = Repo::get()->getIdentityMap()->clear();

        $model = new Model(['id' => 1], State::SAVED);

        $this->assertFalse($map->has($model));

        $map->get($model);

        $this->assertTrue($map->has($model));
    }

    /**
     * @covers ::clear
     */
    public function testClear()
    {
        $map = Repo::get()->getIdentityMap()->clear();

        $map->get(new Model(['id' => 1], State::SAVED));
        $this->assertCount(1, $map->getModels());
        $map->clear();
        $this->assertCount(0, $map->getModels());
    }
}
