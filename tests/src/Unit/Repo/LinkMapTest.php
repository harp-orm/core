<?php

namespace Harp\Core\Test\Unit\Repo;

use Harp\Core\Repo\LinkMap;
use Harp\Core\Repo\LinkOne;

/**
 * @coversDefaultClass Harp\Core\Repo\LinkMap
 *
 * @author     Ivan Kerin <ikerin@gmail.com>
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
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
     * @covers ::addLink
     */
    public function testAddLink()
    {
        $model = new Model();
        $foreign = new Model();
        $link = new LinkOne($model, new RelOne('one', Repo::get(), Repo::get()), $foreign);

        $map = new LinkMap(Repo::get());

        $map->addLink($link);

        $this->assertSame($link, $map->get($model)->get('one'));
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
