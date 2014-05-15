<?php

namespace CL\LunaCore\Test\Unit\Rel;

use CL\LunaCore\Rel\AbstractRel;
use CL\LunaCore\Repo\LinkOne;
use CL\LunaCore\Test\AbstractTestCase;
use SplObjectStorage;

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
     * @covers CL\LunaCore\Rel\AbstractRel::loadForeignForNodes
     */
    public function testLoadForeignForNodes()
    {
        $repo1 = new Repo(__NAMESPACE__.'\Model');
        $repo2 = new Repo(__NAMESPACE__.'\Model');
        $name = 'test name';

        $rel = $this->getMockForAbstractClass(
            'CL\LunaCore\Rel\AbstractRel',
            [$name, $repo1, $repo2]
        );

        $models = [new Model(), new Model()];
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

        $result = $rel->loadForeignForNodes($models);

        $this->assertInternalType('array', $result);
        $this->assertEmpty($result);

        $result = $rel->loadForeignForNodes($models);

        $this->assertSame($expected, $result);
    }

    /**
     * @covers CL\LunaCore\Rel\AbstractRel::loadForeignModels
     */
    public function testLoadForeignModels()
    {
        $repo1 = new Repo(__NAMESPACE__.'\Model');
        $repo2 = new Repo(__NAMESPACE__.'\Model');
        $name = 'test name';

        $models = [new Model(), new Model()];
        $foreign = [new Model(), new Model()];
        $modelLinks = new SplObjectStorage();

        $rel = $this->getMockForAbstractClass(
            'CL\LunaCore\Rel\AbstractRelOne',
            [$name, $repo1, $repo2],
            '',
            true,
            true,
            true,
            ['loadForeignForNodes', 'linkToForeign', 'newLinkFrom']
        );

        $links = [
            new LinkOne($rel, $foreign[0]),
            new LinkOne($rel, $foreign[1]),
        ];

        $rel
            ->expects($this->once())
            ->method('loadForeignForNodes')
            ->with($this->identicalTo($models))
            ->will($this->returnValue($foreign));

        $rel
            ->expects($this->once())
            ->method('linkToForeign')
            ->with($this->identicalTo($models), $this->identicalTo($foreign))
            ->will($this->returnValue($modelLinks));

        $rel
            ->expects($this->at(2))
            ->method('newLinkFrom')
            ->with($this->identicalTo($models[0]), $this->identicalTo($modelLinks))
            ->will($this->returnValue($links[0]));

        $rel
            ->expects($this->at(3))
            ->method('newLinkFrom')
            ->with($this->identicalTo($models[1]), $this->identicalTo($modelLinks))
            ->will($this->returnValue($links[1]));

        $result = $rel->loadForeignModels($models, function($model, $link) use ($models, $links) {
            $this->assertContains($model, $models);
            $this->assertContains($link, $links);
        });
    }
}
