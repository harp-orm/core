<?php

namespace Harp\Core\Test\Integration;

use Harp\Core\Test\Model;

/**
 * @group integration
 * @group integration.custom_link_class
 * @coversNothing
 *
 * @author     Ivan Kerin <ikerin@gmail.com>
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
class CustomLinkClassTest extends AbstractIntegrationTestCase
{
    public function testTest()
    {
        $user1 = Model\User::find(1);

        $posts = $user1->getPosts();

        $this->assertInstanceOf('Harp\Core\Test\Repo\LinkManyPosts', $posts);
    }
}
