<?php

namespace Harp\Core\Test\Integration;

use Harp\Core\Test\Model;

/**
 * @group integration
 * @group integration.identity_map
 * @coversNothing
 *
 * @author     Ivan Kerin <ikerin@gmail.com>
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://spdx.org/licenses/BSD-3-Clause
 */
class IdentityMapTest extends AbstractIntegrationTestCase
{
    public function testTest()
    {
        $user1 = Model\User::find(1);

        $address1 = $user1->getAddress();

        $post1 = $user1->getPosts()->getFirst();

        $user2 = Model\User::find(1);

        $address2 = $user2->getAddress();

        $post2 = $user2->getPosts()->getFirst();

        $address3 = Model\Address::find(1);
        $post3 = Model\Post::find(1);

        $this->assertSame($user1, $user2);
        $this->assertSame($address1, $address2);
        $this->assertSame($post1, $post2);

        $this->assertSame($address1, $address3);
        $this->assertSame($post1, $post3);
    }
}
