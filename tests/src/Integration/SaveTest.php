<?php

namespace Harp\Core\Test\Integration;

use Harp\Core\Test\Model;
use Harp\Core\Test\Repo;
use Harp\Core\Save\Save;

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
                'class' => 'Harp\Core\Test\Model\Post',
            ],
            2 => [
                'id' => 2,
                'name' => 'post 2',
                'body' => 'my post 2',
                'userId' => 1,
                'url' => 'http://example.com/post2',
                'class' => 'Harp\Core\Test\Model\BlogPost',
            ],
            3 => [
                'id' => 3,
                'name' => 'post name',
                'body' => 'some body',
                'userId' => 3,
                'url' => 'http://example.com/postnew',
                'class' => 'Harp\Core\Test\Model\BlogPost',
            ],
            4 => [
                'id' => 4,
                'name' => 'news',
                'body' => 'some other body',
                'userId' => 3,
                'class' => 'Harp\Core\Test\Model\Post',
            ],
        ];

        $this->assertEquals($expectedPostContent, Repo\Post::get()->getContents());

        $contents = Repo\User::get()->getContents();

        $this->assertArrayConstrained(
            [
                'id'        => $this->equalTo(1),
                'name'      => $this->equalTo('changed name'),
                'password'  => $this->equalTo(null),
                'addressId' => $this->equalTo(1),
                'deletedAt' => $this->equalTo($user1->deletedAt),
                'isBlocked' => $this->equalTo(true),
                'profile'   => $this->equalTo('{"firstName":"tester"}'),
            ],
            $contents[1]
        );

        $this->assertArrayConstrained(
            [
                'id'        => $this->equalTo(2),
                'name'      => $this->equalTo('deleted'),
                'password'  => $this->equalTo(null),
                'addressId' => $this->equalTo(1),
                'deletedAt' => $this->equalTo(1401949982),
                'isBlocked' => $this->equalTo(false),
                'profile'   => $this->equalTo(null),
            ],
            $contents[2]
        );

        $this->assertArrayConstrained(
            [
                'id'        => $this->equalTo(3),
                'name'      => $this->equalTo('new name'),
                'password'  => $this->equalTo('test'),
                'addressId' => $this->equalTo(2),
                'deletedAt' => $this->equalTo(null),
                'isBlocked' => $this->equalTo(false),
                'profile'   => $this->equalTo(null),
            ],
            $contents[3]
        );
    }
}
