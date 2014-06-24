<?php

namespace Harp\Core\Test\Model;

use Harp\Core\Model\AbstractModel;
use Harp\Core\Test\Repo;

/**
 * @author     Ivan Kerin <ikerin@gmail.com>
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://spdx.org/licenses/BSD-3-Clause
 */
class Address extends AbstractModel {

    const REPO = 'Harp\Core\Test\Repo\Address';

    public $id;
    public $name;
    public $location;

    public function getUser()
    {
        return $this->getLink('user')->get();
    }

    public function setUser(Address $user)
    {
        $this->getLink('user')->set($user);

        return $this;
    }
}
