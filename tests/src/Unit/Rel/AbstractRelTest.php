<?php

namespace CL\LunaCore\Test\Unit\Rel;

use CL\LunaCore\Rel\AbstractRel;
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

        $rel = $this->getMockForAbstractClass('CL\LunaCore\Rel\AbstractRel', [
            $name,
            $repo1,
            $repo2,
            ['test' => 'test option'],
        ]);

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

        $rel = $this->getMockForAbstractClass('CL\LunaCore\Rel\AbstractRel', [
            $name,
            $repo1,
            $repo2
        ]);

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
}
