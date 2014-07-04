<?php

namespace Harp\Core\Test\Unit\Repo;

use Harp\Core\Test\Repo\TestRepo;

/**
 * @coversDefaultClass Harp\Core\Repo\AbstractLink
 *
 * @author     Ivan Kerin <ikerin@gmail.com>
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://spdx.org/licenses/BSD-3-Clause
 */
class AbstractRelTest extends AbstractRepoTestCase
{
    /**
     * @covers ::__construct
     * @covers ::getModel
     * @covers ::getRel
     */
    public function testConstruct()
    {
        $model = new Model();
        $repo1 = new TestRepo(__NAMESPACE__.'\Model');
        $repo2 = new TestRepo(__NAMESPACE__.'\Model');
        $rel = new RelOne('test', $repo1, $repo2);

        $link = $this->getMockForAbstractClass('Harp\Core\Repo\AbstractLink', [$model, $rel]);
        $this->assertSame($rel, $link->getRel());
        $this->assertSame($model, $link->getModel());
    }
}
