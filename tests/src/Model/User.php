<?php

namespace Harp\Core\Test\Model;

use Harp\Core\Model\SoftDeleteTrait;
use Harp\Core\Test\Rel;
use Harp\Validate\Assert;
use Harp\Serializer;
use Harp\Core\Repo\AbstractRepo;

/**
 * @author     Ivan Kerin <ikerin@gmail.com>
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://spdx.org/licenses/BSD-3-Clause
 */
class User extends AbstractTestModel {

    use SoftDeleteTrait;

    public static function initialize(AbstractRepo $repo)
    {
        SoftDeleteTrait::initialize($repo);

        $repo
            ->setFile('User.json')
            ->addRels([
                new Rel\One('address', $repo, Address::getRepo()),
                (new Rel\Many('posts', $repo, Post::getRepo()))
                    ->setLinkClass(__NAMESPACE__.'\LinkManyPosts'),
            ])
            ->addAsserts([
                new Assert\Present('name'),
            ])
            ->addSerializers([
                new Serializer\Json('profile'),
            ]);
    }

    public $id;
    public $name;
    public $password;
    public $addressId;
    public $isBlocked = false;
    public $profile;

    public function getAddress()
    {
        return $this->get('address');
    }

    public function setAddress(Address $address)
    {
        $this->set('address', $address);

        return $this;
    }

    public function getPosts()
    {
        return $this->all('posts');
    }
}
