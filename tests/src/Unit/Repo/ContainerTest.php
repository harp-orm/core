<?php

namespace Harp\Core\Test\Unit\Repo;

use Harp\Core\Test\Repo\TestRepo;
use Harp\Core\Repo\Container;

/**
 * @coversDefaultClass Harp\Core\Repo\Container
 *
 * @author     Ivan Kerin <ikerin@gmail.com>
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://spdx.org/licenses/BSD-3-Clause
 */
class ContainerTest extends AbstractRepoTestCase
{
    /**
     * @covers ::get
     * @covers ::has
     * @covers ::set
     * @covers ::clear
     */
    public function testGetterSetter()
    {
        $class = __NAMESPACE__.'\Model';

        $this->assertFalse(Container::has($class));
        $repo = Container::get($class);

        $this->assertTrue(Container::has($class));

        $this->assertInstanceOf('Harp\Core\Test\Repo\TestRepo', $repo);
        $this->assertEquals($class, $repo->getModelClass());

        $this->assertSame($repo, Container::get($class));

        $repo2 = new TestRepo($class);

        Container::set($class, $repo2);

        $this->assertSame($repo2, Container::get($class));

        Container::clear();

        $this->assertFalse(Container::has($class));
    }
}
