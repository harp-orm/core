<?php

namespace CL\LunaCore\Test\Unit\Repo;

use CL\LunaCore\Repo\LinkOne;

class LinkOneTest extends AbstractRepoTestCase
{
    /**
     * @covers CL\LunaCore\Repo\LinkOne::__construct
     * @covers CL\LunaCore\Repo\LinkOne::getOriginal
     * @covers CL\LunaCore\Repo\LinkOne::get
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

        $this->assertTrue($result->contains($model));
        $this->assertCount(1, $result);

        $link->set($model2);

        $result = $link->getCurrentAndOriginal();

        $this->assertTrue($result->contains($model));
        $this->assertTrue($result->contains($model2));
        $this->assertCount(2, $result);
    }

}
