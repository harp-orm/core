<?php

namespace Harp\Core\Test\Model;

use Harp\Core\Model\AbstractModel;
use Harp\Core\Test\Repo;

/**
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
class Address extends AbstractModel {

    public function getRepo()
    {
        return Repo\Address::get();
    }

    public $id;
    public $name;
    public $location;

    public function getUser()
    {
        return Repo\Address::get()->loadLink($this, 'user')->get();
    }

    public function setUser(Address $user)
    {
        return Repo\Address::get()->loadLink($this, 'user')->set($user);
    }
}
