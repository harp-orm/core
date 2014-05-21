<?php

namespace CL\LunaCore\Test\Unit\Rel;

use CL\LunaCore\Rel\AbstractRel;
use CL\LunaCore\Rel\AbstractRelMany;
use CL\LunaCore\Model\Models;
use CL\LunaCore\Repo\LinkMany;
use CL\LunaCore\Test\AbstractTestCase;

class AbstractRelTest extends AbstractTestCase
{
    /**
     * @covers CL\LunaCore\Rel\AbstractRel::__construct
     * @covers CL\LunaCore\Rel\AbstractRel::getName
     * @covers CL\LunaCore\Rel\AbstractRel::getRepo
     * @covers CL\LunaCore\Rel\AbstractRel::getForeignRepo
     */
    public function testConstruct()
    {
        $repo1 = new Repo(__NAMESPACE__.'\Model');
        $repo2 = new Repo(__NAMESPACE__.'\Model');
        $name = 'test name';

        $rel = $this->getMockForAbstractClass(
            'CL\LunaCore\Rel\AbstractRel',
            [$name, $repo1, $repo2, ['test' => 'test option']]
        );

        $this->assertSame($name, $rel->getName());
        $this->assertSame($repo1, $rel->getRepo());
        $this->assertSame($repo2, $rel->getForeignRepo());
        $this->assertSame('test option', $rel->test);
    }

    /**
     * @covers CL\LunaCore\Rel\AbstractRel::loadForeignModels
     */
    public function testLoadForeignModels()
    {
        $repo1 = new Repo(__NAMESPACE__.'\Model');
        $repo2 = new Repo(__NAMESPACE__.'\Model');
        $name = 'test name';

        $rel = $this->getMockForAbstractClass(
            'CL\LunaCore\Rel\AbstractRel',
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

        $this->assertInstanceOf('CL\LunaCore\Model\Models', $result);
        $this->assertEmpty($result);

        $result = $rel->loadForeignModels($models);

        $this->assertInstanceOf('CL\LunaCore\Model\Models', $result);
        $this->assertSame($expected, $result->toArray());
    }

    /**
     * @covers CL\LunaCore\Rel\AbstractRel::linkModels
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
            'CL\LunaCore\Rel\AbstractRelMany',
            ['test name', new Repo(__NAMESPACE__.'\Model'), new Repo(__NAMESPACE__.'\Model')],
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
            new LinkMany($rel, [$foreign[0]]),
            new LinkMany($rel, [$foreign[1], $foreign[2]]),
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

        $rel->linkModels(new Models($models), new Models($foreign), function($model, $link) use ($models, $links, & $i) {
            $this->assertSame($models[$i], $model);
            $this->assertSame($links[$i], $link);
            $i++;
        });
    }
}
