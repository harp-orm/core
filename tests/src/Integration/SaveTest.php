<?php

namespace CL\LunaCore\Test\Integration;

use CL\LunaCore\Test\Model;
use CL\LunaCore\Test\Repo;
use CL\LunaCore\Save\Save;

/**
 * @group integration
 * @group integration.save
 * @coversNothing
 */
class SaveTest extends AbstractIntegrationTestCase
{
    public function testTest()
    {
        $user1 = Repo\User::get()->find(1);

        $user1->name = 'changed name';

        $user1->delete();

        $this->assertNotNull($user1->deletedAt);

        $user2 = new Model\User(['name' => 'new name', 'password' => 'test']);
        $user2
            ->setAddress(new Model\Address(['location' => 'here']))
            ->getPosts()
                ->add(new Model\BlogPost(['name' => 'post name', 'body' => 'some body', 'url' => 'http://example.com/postnew']))
                ->add(new Model\Post(['name' => 'news', 'body' => 'some other body']));

        $address = new Model\Address(['name' => 'new name', 'location' => 'new location']);

        $user3 = new Model\User(['name' => 'new name', 'password' => 'test']);

        (new Save())
            ->add($user1)
            ->add($user2)
            ->execute();

        Repo\Address::get()->save($address);

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
                'class' => 'CL\LunaCore\Test\Model\Post',
            ],
            2 => [
                'id' => 2,
                'name' => 'post 2',
                'body' => 'my post 2',
                'userId' => 1,
                'url' => 'http://example.com/post2',
                'class' => 'CL\LunaCore\Test\Model\BlogPost',
            ],
            3 => [
                'id' => 3,
                'name' => 'post name',
                'body' => 'some body',
                'userId' => 3,
                'url' => 'http://example.com/postnew',
                'class' => 'CL\LunaCore\Test\Model\BlogPost',
            ],
            4 => [
                'id' => 4,
                'name' => 'news',
                'body' => 'some other body',
                'userId' => 3,
                'class' => 'CL\LunaCore\Test\Model\Post',
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
                'deletedAt' => $user1->deletedAt,
            ],
            2 => [
                'id' => 2,
                'name' => 'deleted',
                'password' => null,
                'addressId' => 1,
                'isBlocked' => false,
                'deletedAt' => 1400500528,
            ],
            3 => [
                'id' => 3,
                'name' => 'new name',
                'password' => 'test',
                'addressId' => 2,
                'isBlocked' => false,
                'deletedAt' => null,
            ],
        ];

        $this->assertEquals($expectedUserContent, Repo\User::get()->getContents());
    }
}
