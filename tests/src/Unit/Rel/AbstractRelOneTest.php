<?php

namespace CL\LunaCore\Test\Unit\Rel;

use CL\LunaCore\Test\AbstractTestCase;
use CL\LunaCore\Repo\LinkOne;
use CL\LunaCore\Model\Models;
use CL\Util\Objects;

/**
 * @coversDefaultClass CL\LunaCore\Rel\AbstractRelOne
 */
class AbstractRelOneTest extends AbstractTestCase
{

    public function getRel()
    {
        return $this->getMockForAbstractClass(
            'CL\LunaCore\Rel\AbstractRelOne',
            ['test name', new Repo(__NAMESPACE__.'\Model'), new Repo(__NAMESPACE__.'\Model')]
        );
    }

    /**
     * @covers ::newLink
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
     * @covers ::newEmptyLink
     */
    public function testNewEmptyLink()
    {
        $rel = $this->getRel();
        $result = $rel->newEmptyLink();

        $this->assertInstanceof('CL\LunaCore\Repo\LinkOne', $result);
        $this->assertInstanceof(__NAMESPACE__.'\Model', $result->get());
        $this->assertTrue($result->get()->isVoid());
    }

    /**
     * @covers ::newLinkFrom
     */
    public function testNewLinkFrom()
    {
        $links = [];

        $model = new Model();
        $foreign = new Model();

        $rel = $this->getRel();

        $link = $rel->newLinkFrom($model, $links);

        $this->assertInstanceof('CL\LunaCore\Repo\LinkOne', $link);
        $this->assertInstanceof(__NAMESPACE__.'\Model', $link->get());
        $this->assertTrue($link->get()->isVoid());

        $link = $rel->newLinkFrom($model, [$foreign]);

        $this->assertInstanceof('CL\LunaCore\Repo\LinkOne', $link);
        $this->assertInstanceof(__NAMESPACE__.'\Model', $link->get());
        $this->assertSame($foreign, $link->get());
    }
}
