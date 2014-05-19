<?php

namespace CL\LunaCore\Test\Unit\Rel;

use CL\LunaCore\Rel\AbstractRelOne;
use CL\LunaCore\Repo\LinkOne;
use CL\LunaCore\Test\AbstractTestCase;
use CL\Util\Objects;
use SplObjectStorage;

class AbstractRelOneTest extends AbstractTestCase
{

    public function getRel()
    {
        return $this->getMockForAbstractClass(
            AbstractRelOne::class,
            ['test name', new Repo(Model::class), new Repo(Model::class)]
        );
    }

    /**
     * @covers CL\LunaCore\Rel\AbstractRelOne::newLink
     */
    public function testNewLink()
    {
        $expected = new Model(['id' => 1]);
        $expected2 = new Model(['id' => 1]);

        $rel = $this->getRel();
        $result = $rel->newLink($expected);

        $this->assertInstanceof(LinkOne::class, $result);
        $this->assertSame($rel, $result->getRel());
        $this->assertSame($expected, $result->get());

        $result2 = $rel->newLink($expected2);

        $this->assertSame($expected2, $result2->get());
    }

    /**
     * @covers CL\LunaCore\Rel\AbstractRelOne::newEmptyLink
     */
    public function testNewEmptyLink()
    {
        $rel = $this->getRel();
        $result = $rel->newEmptyLink();

        $this->assertInstanceof(LinkOne::class, $result);
        $this->assertInstanceof(Model::class, $result->get());
        $this->assertTrue($result->get()->isVoid());
    }

    /**
     * @covers CL\LunaCore\Rel\AbstractRelOne::newLinkFrom
     */
    public function testNewLinkFrom()
    {
        $links = [];

        $model = new Model();
        $foreign = new Model();

        $rel = $this->getRel();

        $link = $rel->newLinkFrom($model, $links);

        $this->assertInstanceof(LinkOne::class, $link);
        $this->assertInstanceof(Model::class, $link->get());
        $this->assertTrue($link->get()->isVoid());

        $link = $rel->newLinkFrom($model, [$foreign]);

        $this->assertInstanceof(LinkOne::class, $link);
        $this->assertInstanceof(Model::class, $link->get());
        $this->assertSame($foreign, $link->get());
    }
}
