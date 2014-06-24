<?php

namespace Harp\Core\Test\Integration;

use Harp\Core\Test\Model;

/**
 * @group integration
 * @group integration.inheritence
 * @coversNothing
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
