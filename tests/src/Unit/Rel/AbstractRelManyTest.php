<?php

namespace CL\LunaCore\Test\Unit\Rel;

use CL\LunaCore\Rel\AbstractRelOne;
use CL\LunaCore\Test\AbstractTestCase;
use CL\LunaCore\Util\Objects;

class AbstractRelOneTest extends AbstractTestCase
{

    public function getRel()
    {
        return $this->getMockForAbstractClass('CL\LunaCore\Rel\AbstractRelOne', [
            'test name',
            new Repo(__NAMESPACE__.'\Model'),
            new Repo(__NAMESPACE__.'\Model')
        ]);
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

        $this->assertInstanceof('CL\LunaCore\Repo\LinkOne', $result);
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

        $this->assertInstanceof('CL\LunaCore\Repo\LinkOne', $result);
        $this->assertInstanceof('CL\LunaCore\Test\Unit\Rel\Model', $result->get());
        $this->assertTrue($result->get()->isVoid());
    }

    /**
     * @covers CL\LunaCore\Rel\AbstractRelOne::linkToForeign
     */
    public function testLinkToForeign()
    {
        $models = [new Model(), new Model(), new Model()];
        $foreign = [new Model(), new Model()];

        $map = [
            [$models[0], $foreign[0], true],
            [$models[0], $foreign[1], false],
            [$models[1], $foreign[0], false],
            [$models[1], $foreign[1], true],
            [$models[2], $foreign[0], false],
            [$models[2], $foreign[1], false],
        ];

        $rel = $this->getRel();
        $rel
            ->expects($this->exactly(6))
            ->method('areLinked')
            ->will($this->returnValueMap($map));

        $result = $rel->linkToForeign($models, $foreign);

        $this->assertInstanceof('SplObjectStorage', $result);
        $this->assertEquals($foreign[0], $result[$models[0]]);
        $this->assertEquals($foreign[1], $result[$models[1]]);
        $this->assertFalse($result->contains($models[2]));
    }

}
