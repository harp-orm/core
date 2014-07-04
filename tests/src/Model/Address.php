<?php

namespace Harp\Core\Test\Model;

use Harp\Core\Test\Rel;
use Harp\Core\Repo\AbstractRepo;

/**
 * @author     Ivan Kerin <ikerin@gmail.com>
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://spdx.org/licenses/BSD-3-Clause
 */
class Address extends AbstractTestModel {

    public static function initialize(AbstractRepo $repo)
    {
        $repo
            ->setFile('Address.json')
            ->addRels([
                new Rel\One('user', $repo, User::getRepo()),
            ]);
    }

    public $id;
    public $name;
    public $location;

    public function getUser()
    {
        return $this->get('user');
    }

    public function setUser(User $user)
    {
        $this->set('user', $user);

        return $this;
    }
}
