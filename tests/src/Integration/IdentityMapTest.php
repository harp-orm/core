<?php

namespace Harp\Core\Test\Integration;

use Harp\Core\Test\Repo;

/**
 * @group integration
 * @group integration.identity_map
 * @coversNothing
 */
class IdentityMapTest extends AbstractIntegrationTestCase
{
    public function testTest()
    {
        $user1 = Repo\User::get()->find(1);

        $address1 = $user1->getAddress();

        $post1 = $user1->getPosts()->getFirst();

        $user2 = Repo\User::get()->find(1);

        $address2 = $user2->getAddress();

        $post2 = $user2->getPosts()->getFirst();

        $address3 = Repo\Address::get()->find(1);
        $post3 = Repo\Post::get()->find(1);

        $this->assertSame($user1, $user2);
        $this->assertSame($address1, $address2);
        $this->assertSame($post1, $post2);

        $this->assertSame($address1, $address3);
        $this->assertSame($post1, $post3);
    }
}
