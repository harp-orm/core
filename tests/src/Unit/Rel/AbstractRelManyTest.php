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
        $links = [new Model()];

        $model = new Model();

        $rel = $this->getRel();

        $link = $rel->newLinkFrom($model, []);

        $this->assertInstanceof('CL\LunaCore\Repo\LinkMany', $link);
        $this->assertCount(0, $link->all());

        $link = $rel->newLinkFrom($model, $links);

        $this->assertInstanceof('CL\LunaCore\Repo\LinkMany', $link);
        $this->assertCount(1, $link->all());
        $this->assertEquals($links, $link->toArray());
    }

    /**
     * @covers CL\LunaCore\Rel\AbstractRelMany::delete
     */
    public function testDelete()
    {
        $rel = $this->getRel();
        $result = $rel->delete(new Model(), new LinkMany($rel, []));
        $this->assertEquals(new Models(), $result);
    }

    /**
     * @covers CL\LunaCore\Rel\AbstractRelMany::insert
     */
    public function testInsert()
    {
        $rel = $this->getRel();
        $result = $rel->insert(new Model(), new LinkMany($rel, []));
        $this->assertEquals(new Models(), $result);
    }

    /**
     * @covers CL\LunaCore\Rel\AbstractRelMany::update
     */
    public function testUpdate()
    {
        $rel = $this->getRel();
        $result = $rel->update(new Model(), new LinkMany($rel, []));
        $this->assertNull($result);
    }

}
