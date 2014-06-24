<?php

namespace Harp\Core\Test\Integration;

use Harp\Core\Test\Model;

/**
 * @group integration
 * @group integration.inheritence
 * @coversNothing
 *
 * @author     Ivan Kerin <ikerin@gmail.com>
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://spdx.org/licenses/BSD-3-Clause
 */
class InheritenceTest extends AbstractIntegrationTestCase
{
    public function testInheritence()
    {
        $post1 = Model\Post::find(1);
        $post2 = Model\BlogPost::find(1);

        $this->assertSame($post1, $post2);
    }
}
