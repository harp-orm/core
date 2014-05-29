<?php

namespace Harp\Core\Test\Unit\Repo;

use Harp\Core\Repo\LinkMap;

/**
 * @coversDefaultClass Harp\Core\Repo\LinkMap
 */
class LinkMapTest extends AbstractRepoTestCase
{
    /**
     * @covers ::get
     * @covers ::has
     * @covers ::getRepo
     * @covers ::isEmpty
     * @covers ::__construct
     */
    public function testTest()
    {
        $map = new LinkMap(Repo::get());
        $model = new Model();

        $this->assertFalse($map->has($model));
        $this->assertSame(Repo::get(), $map->getRepo());
        $this->assertTrue($map->isEmpty($model));

        $links = $map->get($model);

        $this->assertInstanceOf('Harp\Core\Repo\Links', $links);
        $this->assertEmpty($links->all());
        $this->assertTrue($map->has($model));
        $this->assertTrue($map->isEmpty($model));

        $links->add($this->getLinkOne());

        $this->assertTrue($map->has($model));
        $this->assertFalse($map->isEmpty($model));

        $links2 = $map->get($model);

        $this->assertSame($links, $links2);
    }

    /**
     * @covers ::get
     * @expectedException InvalidArgumentException
     */
    public function testInvalidModel()
    {
        $map = new LinkMap(Repo::get());
        $model = new ModelOther();

        $map->get($model);
    }
}
