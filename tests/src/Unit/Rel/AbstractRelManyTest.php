<?php

namespace Harp\Core\Test\Unit\Rel;

use Harp\Core\Rel\AbstractRelMany;
use Harp\Core\Repo\LinkMany;
use Harp\Core\Test\AbstractTestCase;
use Harp\Core\Model\Models;
use Harp\Util\Objects;

/**
 * @coversDefaultClass Harp\Core\Rel\AbstractRelMany
 */
class AbstractRelManyTest extends AbstractTestCase
{
    public function getRel()
    {
        return $this->getMockForAbstractClass(
            'Harp\Core\Rel\AbstractRelMany',
            ['test name', new Repo(__NAMESPACE__.'\Model'), new Repo(__NAMESPACE__.'\Model')]
        );
    }

    /**
     * @covers ::newLink
     */
    public function testNewLink()
    {
        $expected = [new Model(['id' => 1]), new Model(['id' => 2])];
        $expected2 = [new Model(['id' => 1]), new Model(['id' => 2])];

        $rel = $this->getRel();
        $result = $rel->newLink($expected);

        $this->assertInstanceof('Harp\Core\Repo\LinkMany', $result);
        $this->assertSame($expected, $result->toArray());

        $result2 = $rel->newLink($expected2);

        $this->assertSame($expected2, $result2->toArray(), 'Should pass through identity mapper');
    }

    /**
     * @covers ::newEmptyLink
     */
    public function testNewEmptyLink()
    {
        $rel = $this->getRel();
        $result = $rel->newEmptyLink();

        $this->assertInstanceof('Harp\Core\Repo\LinkMany', $result);
        $this->assertCount(0, $result);
    }

    /**
     * @covers ::newLinkFrom
     */
    public function testNewLinkFrom()
    {
        $models = [new Model()];

        $model = new Model();

        $rel = $this->getRel();

        $link = $rel->newLinkFrom($model, []);

        $this->assertInstanceof('Harp\Core\Repo\LinkMany', $link);
        $this->assertCount(0, $link);

        $link = $rel->newLinkFrom($model, $models);

        $this->assertInstanceof('Harp\Core\Repo\LinkMany', $link);
        $this->assertCount(1, $link);
        $this->assertEquals($models, $link->toArray());
    }
}
