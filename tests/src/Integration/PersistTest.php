<?php

namespace CL\LunaCore\Test\Integration;

use CL\LunaCore\Test\Model;
use CL\LunaCore\Test\Repo;
use CL\LunaCore\Repo\Persist;

/**
 * @group integration
 * @group integration.persist
 * @coversNothing
 */
class PersistTest extends AbstractIntegrationTestCase
{
    public function testTest()
    {
        $user1 = Repo\User::get()->find(1);

        $user1->name = 'changed name';

        $user2 = new Model\User(['name' => 'new name', 'password' => 'test']);
        $user2
            ->setAddress(new Model\Address(['location' => 'here']))
            ->getPosts()
                ->add(new Model\Post(['name' => 'post name', 'body' => 'some body']))
                ->add(new Model\Post(['name' => 'news', 'body' => 'some other body']));

        $address = new Model\Address(['name' => 'new name', 'location' => 'new location']);

        (new Persist())
            ->add($user1)
            ->add($user2)
            ->execute();

        Repo\Address::get()->persist($address);

        $expectedAddressContent = [
            1 => [
                'id' => 1,
                'name' => null,
                'location' => 'test location',
            ],
            2 => [
                'id' => 2,
                'name' => null,
                'location' => 'here',
            ],
            3 => [
                'id' => 3,
                'name' => 'new name',
                'location' => 'new location',
            ],
        ];

        $this->assertEquals($expectedAddressContent, Repo\Address::get()->getContents());

        $expectedPostContent = [
            1 => [
                'id' => 1,
                'name' => 'post 1',
                'body' => 'my post 1',
                'userId' => 1,
            ],
            2 => [
                'id' => 2,
                'name' => 'post 2',
                'body' => 'my post 2',
                'userId' => 1,
            ],
            3 => [
                'id' => 3,
                'name' => 'post name',
                'body' => 'some body',
                'userId' => 2,
            ],
            4 => [
                'id' => 4,
                'name' => 'news',
                'body' => 'some other body',
                'userId' => 2,
            ],
        ];

        $this->assertEquals($expectedPostContent, Repo\Post::get()->getContents());

        $expectedUserContent = [
            1 => [
                'id' => 1,
                'name' => 'changed name',
                'password' => null,
                'addressId' => 1,
                'isBlocked' => true,
            ],
            2 => [
                'id' => 2,
                'name' => 'new name',
                'password' => 'test',
                'addressId' => 2,
                'isBlocked' => false,
            ],
        ];

        $this->assertEquals($expectedUserContent, Repo\User::get()->getContents());

    }
}
