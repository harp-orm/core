<?php

namespace CL\LunaCore\Test\Unit\Repo;

/**
 * @coversDefaultClass CL\LunaCore\Repo\AbstractLink
 */
class AbstractRelTest extends AbstractRepoTestCase
{
    /**
     * @covers ::__construct
     * @covers ::getRel
     */
    public function testConstruct()
    {
        $repo1 = new Repo(__NAMESPACE__.'\Model');
        $repo2 = new Repo(__NAMESPACE__.'\Model');
        $rel = new RelOne('test', $repo1, $repo2);

        $link = $this->getMockForAbstractClass('CL\LunaCore\Repo\AbstractLink', [$rel]);
        $this->assertSame($rel, $link->getRel());
    }
}
