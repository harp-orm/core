<?php

namespace Harp\Core\Test\Integration;

use Harp\Core\Test\Repo;

/**
 * @group integration
 * @group integration.inheritence
 * @coversNothing
 */
class InheritenceTest extends AbstractIntegrationTestCase
{
    public function testInheritence()
    {
        $post1 = Repo\Post::get()->find(1);
        $post2 = Repo\BlogPost::get()->find(1);

        $this->assertSame($post1, $post2);
    }
}
