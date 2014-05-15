<?php

namespace CL\LunaCore\Test\Unit\Rel;

use CL\LunaCore\Rel\AbstractRelMany;
use CL\LunaCore\Test\AbstractTestCase;
use CL\LunaCore\Util\Objects;
use SplObjectStorage;

class AbstractRelManyTest extends AbstractTestCase
{
    public function getRel()
    {
        return $this->getMockForAbstractClass('CL\LunaCore\Rel\AbstractRelMany', [
            'test name',
            new Repo(__NAMESPACE__.'\Model'),
            new Repo(__NAMESPACE__.'\Model')
        ]);
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

        $this->assertInstanceof('CL\LunaCore\Repo\LinkMany', $result);
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

        $this->assertInstanceof('CL\LunaCore\Repo\LinkMany', $result);
        $this->assertCount(0, $result);
    }

    /**
     * @covers CL\LunaCore\Rel\AbstractRelMany::newLinkFrom
     */
    public function testNewLinkFrom()
    {
        $links = new SplObjectStorage();

        $model = new Model();
        $foreign = new Model();

        $rel = $this->getRel();

        $link = $rel->newLinkFrom($model, $links);

        $this->assertInstanceof('CL\LunaCore\Repo\LinkMany', $link);
        $this->assertCount(0, $link->all());

        $links[$model] = [$foreign];

        $link = $rel->newLinkFrom($model, $links);

        $this->assertInstanceof('CL\LunaCore\Repo\LinkMany', $link);
        $this->assertCount(1, $link->all());
        $this->assertTrue($link->all()->contains($foreign));
    }

    /**
     * @covers CL\LunaCore\Rel\AbstractRelMany::linkToForeign
     */
    public function testLinkToForeign()
    {
        $models = [new Model(), new Model()];
        $foreign = [new Model(), new Model(), new Model()];

        $map = [
            [$models[0], $foreign[0], true],
            [$models[0], $foreign[1], false],
            [$models[0], $foreign[2], false],
            [$models[1], $foreign[0], false],
            [$models[1], $foreign[1], true],
            [$models[1], $foreign[2], true],
        ];

        $rel = $this->getRel();
        $rel
            ->expects($this->exactly(6))
            ->method('areLinked')
            ->will($this->returnValueMap($map));


        $result = $rel->linkToForeign($models, $foreign);

        $this->assertInstanceof('SplObjectStorage', $result);
        $this->assertEquals([$foreign[0]], $result[$models[0]]);
        $this->assertEquals([$foreign[1], $foreign[1]], $result[$models[1]]);
    }

}
