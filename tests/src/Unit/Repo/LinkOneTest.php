<?php

namespace CL\LunaCore\Test\Unit\Repo;

use CL\LunaCore\Repo\LinkOne;
use CL\LunaCore\Model\Models;

class LinkOneTest extends AbstractRepoTestCase
{
    /**
     * @covers CL\LunaCore\Repo\LinkOne::__construct
     * @covers CL\LunaCore\Repo\LinkOne::getOriginal
     * @covers CL\LunaCore\Repo\LinkOne::get
     * @covers CL\LunaCore\Repo\LinkOne::getRel
     */
    public function testConstruct()
    {
        $rel = $this->getRelOne();
        $model = new Model();

        $link = new LinkOne($rel, $model);

        $this->assertSame($rel, $link->getRel());
        $this->assertSame($model, $link->get());
        $this->assertSame($model, $link->getOriginal());
    }

    /**
     * @covers CL\LunaCore\Repo\LinkOne::delete
     */
    public function testDelete()
    {
        $model = new Model();

        $rel = $this->getMock(
            __NAMESPACE__.'\RelOne',
            ['delete'],
            ['test', new Repo(__NAMESPACE__.'\Model'), new Repo(__NAMESPACE__.'\Model')]
        );

        $link = new LinkOne($rel, $model);

        $models = new Models();

        $rel
            ->expects($this->once())
            ->method('delete')
            ->with($this->identicalTo($model), $this->identicalTo($link))
            ->will($this->returnValue($models));

        $result = $link->delete($model);
        $this->assertSame($models, $result);
    }

    /**
     * @covers CL\LunaCore\Repo\LinkOne::insert
     */
    public function testInsert()
    {
        $model = new Model();

        $rel = $this->getMock(
            __NAMESPACE__.'\RelOne',
            ['insert'],
            ['test', new Repo(__NAMESPACE__.'\Model'), new Repo(__NAMESPACE__.'\Model')]
        );

        $link = new LinkOne($rel, $model);

        $models = new Models();

        $rel
            ->expects($this->once())
            ->method('insert')
            ->with($this->identicalTo($model), $this->identicalTo($link))
            ->will($this->returnValue($models));

        $reuslt = $link->insert($model);
        $this->assertSame($models, $reuslt);
    }

    /**
     * @covers CL\LunaCore\Repo\LinkOne::update
     */
    public function testUpdate()
    {
        $model = new Model();

        $rel = $this->getMock(
            __NAMESPACE__.'\RelOne',
            ['update'],
            ['test', new Repo(__NAMESPACE__.'\Model'), new Repo(__NAMESPACE__.'\Model')]
        );

        $link = new LinkOne($rel, $model);

        $models = new Models();

        $rel
            ->expects($this->once())
            ->method('update')
            ->with($this->identicalTo($model), $this->identicalTo($link))
            ->will($this->returnValue($models));

        $link->update($model);
    }

    /**
     * @covers CL\LunaCore\Repo\LinkOne::set
     * @covers CL\LunaCore\Repo\LinkOne::get
     * @covers CL\LunaCore\Repo\LinkOne::isChanged
     * @covers CL\LunaCore\Repo\LinkOne::getOriginal
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
     * @covers CL\LunaCore\Repo\LinkOne::clear
     */
    public function testClear()
    {
        $link = $this->getLinkOne();
        $link->clear();

        $this->assertTrue($link->get()->isVoid());
    }

    /**
     * @covers CL\LunaCore\Repo\LinkOne::getCurrentAndOriginal
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
