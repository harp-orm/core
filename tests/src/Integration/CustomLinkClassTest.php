<?php

namespace Harp\Core\Test\Integration;

use Harp\Core\Test\Repo;

/**
 * @group integration
 * @group integration.custom_link_class
 * @coversNothing
 */
class CustomLinkClassTest extends AbstractIntegrationTestCase
{
    public function testTest()
    {
        $user1 = Repo\User::get()->find(1);

        $posts = $user1->getPosts();

        $this->assertInstanceOf('Harp\Core\Test\Repo\LinkManyPosts', $posts);
    }
}
