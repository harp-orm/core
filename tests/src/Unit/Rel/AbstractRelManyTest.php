<?php

namespace CL\LunaCore\Test\Unit\Rel;

use CL\LunaCore\Rel\AbstractRelMany;
use CL\LunaCore\Repo\LinkMany;
use CL\LunaCore\Test\AbstractTestCase;
use CL\LunaCore\Model\Models;
use CL\Util\Objects;


class AbstractRelManyTest extends AbstractTestCase
{
    public function getRel()
    {
        return $this->getMockForAbstractClass(
            'CL\LunaCore\Rel\AbstractRelMany',
            ['test name', new Repo(__NAMESPACE__.'\Model'), new Repo(__NAMESPACE__.'\Model')]
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

        $this->assertInstanceof('CL\LunaCore\Repo\LinkMany', $result);
        $this->assertSame($expected, $result->toArray());

        $result2 = $rel->newLink($expected2);

        $this->assertSame($expected2, $result2->toArray(), 'Should pass through identity mapper');
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
        $models = [new Model()];

        $model = new Model();

        $rel = $this->getRel();

        $link = $rel->newLinkFrom($model, []);

        $this->assertInstanceof('CL\LunaCore\Repo\LinkMany', $link);
        $this->assertCount(0, $link);

        $link = $rel->newLinkFrom($model, $models);

        $this->assertInstanceof('CL\LunaCore\Repo\LinkMany', $link);
        $this->assertCount(1, $link);
        $this->assertEquals($models, $link->toArray());
    }
}
