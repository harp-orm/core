<?php

namespace CL\LunaCore\Test\Unit\Rel;

use CL\LunaCore\Rel\AbstractRelMany;
use CL\LunaCore\Repo\LinkMany;
use CL\LunaCore\Test\AbstractTestCase;
use CL\Util\Objects;
use SplObjectStorage;

class AbstractRelManyTest extends AbstractTestCase
{
    public function getRel()
    {
        return $this->getMockForAbstractClass(
            AbstractRelMany::class,
            ['test name', new Repo(Model::class), new Repo(Model::class)]
        );
    }

    /**
     * @covers CL\LunaCore\Rel\AbstractRelMany::newLink
     */
    public function testNewLink()
    {
        $expected = [new Model(['id' => 1]), new Model(['id' => 2])];
        $expected2 = [new Model(['id' => 1]), new Model(['id' => 2])];

        $rel = $this->getRel();
        $result = $rel->newLink($expected);

        $this->assertInstanceof(LinkMany::class, $result);
        $models = Objects::toArray($result->all());
        $this->assertSame($expected, $models);

        $result2 = $rel->newLink($expected2);

        $models2 = Objects::toArray($result2->all());
        $this->assertSame($expected2, $models2, 'Should pass through identity mapper');
    }

    /**
     * @covers CL\LunaCore\Rel\AbstractRelMany::newEmptyLink
     */
    public function testNewEmptyLink()
    {
        $rel = $this->getRel();
        $result = $rel->newEmptyLink();

        $this->assertInstanceof(LinkMany::class, $result);
        $this->assertCount(0, $result);
    }

    /**
     * @covers CL\LunaCore\Rel\AbstractRelMany::newLinkFrom
     */
    public function testNewLinkFrom()
    {
        $links = [new Model()];

        $model = new Model();

        $rel = $this->getRel();

        $link = $rel->newLinkFrom($model, []);

        $this->assertInstanceof(LinkMany::class, $link);
        $this->assertCount(0, $link->all());

        $link = $rel->newLinkFrom($model, $links);

        $this->assertInstanceof(LinkMany::class, $link);
        $this->assertCount(1, $link->all());
        $this->assertEquals($links, $link->toArray());
    }
}
