<?php

namespace Harp\Core\Test\Integration;

use Harp\Core\Test\Model;
use Harp\Core\Test\Repo;
use Harp\Core\Save\Save;

/**
 * @group integration
 * @group integration.serializer
 * @coversNothing
 */
class SerializerTest extends AbstractIntegrationTestCase
{
    public function testTest()
    {
        $user = Repo\User::get()->find(1);

        $this->assertEquals(array('firstName' => 'tester'), $user->profile);

        $user->profile = array('firstName' => 'new', 'lastName' => 'user');

        Repo\User::get()->save($user);

        $contents = Repo\User::get()->getContents();

        $this->assertEquals('{"firstName":"new","lastName":"user"}', $contents[1]['profile']);
    }
}
