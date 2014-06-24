<?php

namespace Harp\Core\Test\Unit\Repo;

use Harp\Core\Repo\LinkOne;
use Harp\Core\Model\Models;

/**
 * @coversDefaultClass Harp\Core\Repo\LinkOne
 *
 * @author     Ivan Kerin <ikerin@gmail.com>
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
class LinkOneTest extends AbstractRepoTestCase
{
    /**
     * @covers ::__construct
     * @covers ::getOriginal
     * @covers ::getRel
     * @covers ::get
     */
    public function testConstruct()
    {
        $rel = $this->getRelOne();
        $model = new Model();

        $link = new LinkOne(new Model(), $rel, $model);

        $this->assertSame($rel, $link->getRel());
        $this->assertSame($model, $link->get());
        $this->assertSame($model, $link->getOriginal());
    }

    /**
     * @covers ::delete
     */
    public function testDelete()
    {
        $model = new Model();

        $rel = $this->getMock(
            __NAMESPACE__.'\RelOne',
            ['delete'],
            ['test', new Repo(), new Repo()]
        );

        $link = new LinkOne($model, $rel, $model);

        $models = new Models();

        $rel
            ->expects($this->once())
            ->method('delete')
            ->with($this->identicalTo($link))
            ->will($this->returnValue($models));

        $result = $link->delete();
        $this->assertSame($models, $result);
    }

    /**
     * @covers ::delete
     */
    public function testNoDelete()
    {
        $rel = $this->getMockForAbstractClass(
            'Harp\Core\Rel\AbstractRelOne',
            ['test', new Repo(), new Repo()]
        );

        $model = new Model();
        $link = new LinkOne($model, $rel, new Model());
        $models = new Models();

        $result = $link->delete();
        $this->assertNull($result);
    }

    /**
     * @covers ::insert
     */
    public function testInsert()
    {
        $model = new Model();

        $rel = $this->getMock(
            __NAMESPACE__.'\RelOne',
            ['insert'],
            ['test', new Repo(), new Repo()]
        );

        $link = new LinkOne($model, $rel, $model);

        $models = new Models();

        $rel
            ->expects($this->once())
            ->method('insert')
            ->with($this->identicalTo($link))
            ->will($this->returnValue($models));

        $reuslt = $link->insert();
        $this->assertSame($models, $reuslt);
    }

    /**
     * @covers ::insert
     */
    public function testNoInsert()
    {
        $rel = $this->getMockForAbstractClass(
            'Harp\Core\Rel\AbstractRelOne',
            ['test', new Repo(), new Repo()]
        );

        $model = new Model();
        $link = new LinkOne($model, $rel, new Model());
        $models = new Models();

        $result = $link->insert();
        $this->assertNull($result);
    }

    /**
     * @covers ::update
     */
    public function testUpdate()
    {
        $model = new Model();

        $rel = $this->getMock(
            __NAMESPACE__.'\RelOne',
            ['update'],
            ['test', new Repo(), new Repo()]
        );

        $link = new LinkOne($model, $rel, $model);

        $models = new Models();

        $rel
            ->expects($this->once())
            ->method('update')
            ->with($this->identicalTo($link))
            ->will($this->returnValue($models));

        $reuslt = $link->update();
        $this->assertSame($models, $reuslt);

    }

    /**
     * @covers ::update
     */
    public function testNoUpdate()
    {
        $rel = $this->getMockForAbstractClass(
            'Harp\Core\Rel\AbstractRelOne',
            ['test', new Repo(), new Repo()]
        );

        $model = new Model();
        $link = new LinkOne($model, $rel, new Model());
        $models = new Models();

        $result = $link->update();
        $this->assertNull($result);
    }

    /**
     * @covers ::set
     * @covers ::get
     * @covers ::isChanged
     * @covers ::getOriginal
     */
    public function testSet()
    {
        $link = $this->getLinkOne();
        $model = $link->get();
        $model2 = new Model();

        $this->assertFalse($link->isChanged());

        $link->set($model);

        $this->assertFalse($link->isChanged());

        $link->set($model2);

        $this->assertTrue($link->isChanged());
        $this->assertSame($model2, $link->get());
        $this->assertSame($model, $link->getOriginal());
    }

    /**
     * @covers ::clear
     */
    public function testClear()
    {
        $link = $this->getLinkOne();
        $link->clear();

        $this->assertTrue($link->get()->isVoid());
    }

    /**
     * @covers ::getCurrentAndOriginal
     */
    public function testGetCurrentAndOriginal()
    {
        $link = $this->getLinkOne();

        $model = $link->get();
        $model2 = new Model();

        $result = $link->getCurrentAndOriginal();

        $this->assertTrue($result->has($model));
        $this->assertCount(1, $result);

        $link->set($model2);

        $result = $link->getCurrentAndOriginal();

        $this->assertTrue($result->has($model));
        $this->assertTrue($result->has($model2));
        $this->assertCount(2, $result);
    }

}
