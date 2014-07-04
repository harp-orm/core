<?php

namespace Harp\Core\Test\Model;

use Harp\Core\Model\InheritedTrait;
use Harp\Core\Test\Rel;
use Harp\Core\Repo\AbstractRepo;

/**
 * @author     Ivan Kerin <ikerin@gmail.com>
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://spdx.org/licenses/BSD-3-Clause
 */
class Post extends AbstractTestModel
{
    use InheritedTrait;

    public static function initialize(AbstractRepo $repo)
    {
        InheritedTrait::initialize($repo);

        $repo
            ->setFile('Post.json')
            ->addRels([
                new Rel\One('user', $repo, User::getRepo()),
            ]);
    }

    public $id;
    public $name;
    public $body;
    public $userId;

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
