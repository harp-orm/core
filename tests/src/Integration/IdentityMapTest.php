<?php

namespace CL\LunaCore\Test;

use CL\LunaCore\Test\Repo;

class IdentityMapTest extends AbstractTestCase
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
