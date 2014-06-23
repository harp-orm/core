<?php

namespace Harp\Core\Test\Unit\Rel;

use Harp\Core\Test\AbstractTestCase;
use Harp\Core\Repo\LinkOne;
use Harp\Core\Model\Models;
use Harp\Util\Objects;

/**
 * @coversDefaultClass Harp\Core\Rel\AbstractRelOne
 */
class AbstractRelOneTest extends AbstractTestCase
{

    public function getRel()
    {
        return $this->getMockForAbstractClass(
            'Harp\Core\Rel\AbstractRelOne',
            ['test name', new Repo(), new Repo()]
        );
    }

    /**
     * @covers ::newLinkFrom
     */
    public function testNewLink()
    {
        $expected = new Model(['id' => 1]);
        $expected2 = new Model(['id' => 1]);
        $model = new Model();

        $rel = $this->getRel();
        $result = $rel->newLinkFrom($model, [$expected]);

        $this->assertInstanceof('Harp\Core\Repo\LinkOne', $result);
        $this->assertSame($rel, $result->getRel());
        $this->assertSame($expected, $result->get());

        $result2 = $rel->newLinkFrom($model, [$expected2]);

        $this->assertSame($expected2, $result2->get());

        $result3 = $rel->newLinkFrom($model, []);

        $this->assertInstanceof('Harp\Core\Repo\LinkOne', $result3);
        $this->assertInstanceof(__NAMESPACE__.'\Model', $result3->get());
        $this->assertTrue($result3->get()->isVoid());

    }
}
