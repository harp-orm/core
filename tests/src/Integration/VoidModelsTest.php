<?php

namespace Harp\Core\Test\Integration;

use Harp\Core\Test\Model;

/**
 * @group integration
 * @group integration.void_models
 * @coversNothing
 *
 * @author     Ivan Kerin <ikerin@gmail.com>
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
class VoidModelsTest extends AbstractIntegrationTestCase
{
    public function testRels()
    {
        $user = Model\User::find(1231421);

        $this->assertInstanceOf('Harp\Core\Test\Model\User', $user);
        $this->assertTrue($user->isVoid());

        $address = $user->getAddress();

        $this->assertInstanceOf('Harp\Core\Test\Model\Address', $address);
        $this->assertTrue($address->isVoid());

        $post = $user->getPosts()->getFirst();

        $this->assertInstanceOf('Harp\Core\Test\Model\Post', $post);
        $this->assertTrue($post->isVoid());

        $user = $post->getUser();

        $this->assertInstanceOf('Harp\Core\Test\Model\User', $user);
        $this->assertTrue($user->isVoid());
    }
}
