<?php

namespace Harp\Core\Test\Model;

use Harp\Core\Model\AbstractModel;
use Harp\Core\Model\SoftDeleteTrait;
use Harp\Core\Test\Repo;

/**
 * @author     Ivan Kerin <ikerin@gmail.com>
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://spdx.org/licenses/BSD-3-Clause
 */
class User extends AbstractModel {

    use SoftDeleteTrait;

    const REPO = 'Harp\Core\Test\Repo\User';

    public $id;
    public $name;
    public $password;
    public $addressId;
    public $isBlocked = false;
    public $profile;

    public function getAddress()
    {
        return $this->getLinkedModel('address');
    }

    public function setAddress(Address $address)
    {
        $this->setLinkedModel('address', $address);

        return $this;
    }

    public function getPosts()
    {
        return $this->getLinkMany('posts');
    }
}
