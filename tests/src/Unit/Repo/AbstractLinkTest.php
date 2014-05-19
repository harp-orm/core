<?php

namespace CL\LunaCore\Test\Unit\Repo;

use CL\LunaCore\Repo\AbstractLink;

class AbstractRelTest extends AbstractRepoTestCase
{
    /**
     * @covers CL\LunaCore\Repo\AbstractLink::__construct
     * @covers CL\LunaCore\Repo\AbstractLink::getRel
     */
    public function testConstruct()
    {
        $repo1 = new Repo(Model::class);
        $repo2 = new Repo(Model::class);
        $rel = new RelOne('test', $repo1, $repo2);

        $link = $this->getMockForAbstractClass(AbstractLink::class, [$rel]);
        $this->assertSame($rel, $link->getRel());
    }
}
