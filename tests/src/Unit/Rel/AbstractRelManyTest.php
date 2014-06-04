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
     * @covers ::newLinkFrom
     */
    public function testNewLinkFrom()
    {
        $expected = [new Model(['id' => 1]), new Model(['id' => 2])];
        $expected2 = [new Model(['id' => 1]), new Model(['id' => 2])];
        $expected3 = [];

        $rel = $this->getRel();
        $result = $rel->newLinkFrom($expected);

        $this->assertInstanceof('Harp\Core\Repo\LinkMany', $result);
        $this->assertSame($expected, $result->toArray());

        $result2 = $rel->newLinkFrom($expected2);

        $this->assertSame($expected2, $result2->toArray(), 'Should pass through identity mapper');

        $result3 = $rel->newLinkFrom($expected3);

        $this->assertSame($expected3, $result3->toArray(), 'Should allow empty');
    }
}
