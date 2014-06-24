<?php

namespace Harp\Core\Test\Unit\Repo;

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
        $repo1 = new Repo();
        $repo2 = new Repo();
        $rel = new RelOne('test', $repo1, $repo2);

        $link = $this->getMockForAbstractClass('Harp\Core\Repo\AbstractLink', [$model, $rel]);
        $this->assertSame($rel, $link->getRel());
        $this->assertSame($model, $link->getModel());
    }
}
