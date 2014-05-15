<?php

namespace CL\LunaCore\Test\Unit\Repo;

use CL\LunaCore\Repo\LinkMap;

class LinkMapTest extends AbstractRepoTestCase
{
    /**
     * @covers CL\LunaCore\Repo\LinkMap::get
     * @covers CL\LunaCore\Repo\LinkMap::has
     * @covers CL\LunaCore\Repo\LinkMap::isEmpty
     * @covers CL\LunaCore\Repo\LinkMap::__construct
     */
    public function testTest()
    {
        $map = new LinkMap();
        $model = new Model();

        $this->assertFalse($map->has($model));
        $this->assertTrue($map->isEmpty($model));

        $links = $map->get($model);

        $this->assertInstanceOf('CL\LunaCore\Repo\Links', $links);
        $this->assertEmpty($links->all());
        $this->assertTrue($map->has($model));
        $this->assertTrue($map->isEmpty($model));

        $links->add($this->getLinkOne());

        $this->assertTrue($map->has($model));
        $this->assertFalse($map->isEmpty($model));

        $links2 = $map->get($model);

        $this->assertSame($links, $links2);
    }
}
