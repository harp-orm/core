<?php

namespace Harp\Core\Test\Unit\Rel;

use Harp\Core\Rel\AbstractRel;
use Harp\Core\Rel\AbstractRelMany;
use Harp\Core\Model\Models;
use Harp\Core\Repo\LinkMany;
use Harp\Core\Test\Repo\TestRepo;
use Harp\Core\Test\AbstractTestCase;

/**
 * @coversDefaultClass Harp\Core\Rel\AbstractRel
 *
 * @author     Ivan Kerin <ikerin@gmail.com>
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://spdx.org/licenses/BSD-3-Clause
 */
class AbstractRelTest extends AbstractTestCase
{
    /**
     * @covers ::__construct
     * @covers ::getName
     * @covers ::getRepo
     * @covers ::getForeignRepo
     */
    public function testConstruct()
    {
        $repo1 = new TestRepo(__NAMESPACE__.'\Model');
        $repo2 = new TestRepo(__NAMESPACE__.'\Model');
        $name = 'test name';

        $rel = $this->getMockForAbstractClass(
            'Harp\Core\Rel\AbstractRel',
            [$name, $repo1, $repo2, ['test' => 'test option']]
        );

        $this->assertSame($name, $rel->getName());
        $this->assertSame($repo1, $rel->getRepo());
        $this->assertSame($repo2, $rel->getForeignRepo());
        $this->assertSame('test option', $rel->test);
    }

    /**
     * @covers ::loadForeignModels
     */
    public function testLoadForeignModels()
    {
        $repo1 = new TestRepo(__NAMESPACE__.'\Model');
        $repo2 = new TestRepo(__NAMESPACE__.'\Model');
        $name = 'test name';

        $rel = $this->getMockForAbstractClass(
            'Harp\Core\Rel\AbstractRel',
            [$name, $repo1, $repo2]
        );

        $models = new Models([new Model(), new Model()]);
        $expected = [new Model(), new Model(), new Model()];

        $rel
            ->expects($this->exactly(2))
            ->method('hasForeign')
            ->with($this->identicalTo($models))
            ->will($this->onConsecutiveCalls(false, true));

        $rel
            ->expects($this->once())
            ->method('loadForeign')
            ->with($this->identicalTo($models))
            ->will($this->returnValue($expected));

        $result = $rel->loadForeignModels($models);

        $this->assertInstanceOf('Harp\Core\Model\Models', $result);
        $this->assertEmpty($result);

        $result = $rel->loadForeignModels($models);

        $this->assertInstanceOf('Harp\Core\Model\Models', $result);
        $this->assertSame($expected, $result->toArray());
    }

    /**
     * @covers ::linkModels
     */
    public function testLinkModels()
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

        $rel = $this->getMockForAbstractClass(
            'Harp\Core\Rel\AbstractRelMany',
            ['test name', new TestRepo(__NAMESPACE__.'\Model'), new TestRepo(__NAMESPACE__.'\Model')],
            '',
            true,
            true,
            true,
            ['newLinkFrom']
        );

        $rel
            ->expects($this->exactly(6))
            ->method('areLinked')
            ->will($this->returnValueMap($map));

        $links = [
            new LinkMany($models[0], $rel, [$foreign[0]]),
            new LinkMany($models[1], $rel, [$foreign[1], $foreign[2]]),
        ];


        $linkMap = [
            [$models[0], [$foreign[0]], $links[0]],
            [$models[1], [$foreign[1], $foreign[2]], $links[1]],
        ];

        $rel
            ->expects($this->exactly(2))
            ->method('newLinkFrom')
            ->will($this->returnValueMap($linkMap));

        $i = 0;

        $rel->linkModels(new Models($models), new Models($foreign), function($link) use ($models, $links, & $i) {
            $this->assertSame($models[$i], $link->getModel());
            $this->assertSame($links[$i], $link);
            $i++;
        });
    }
}
