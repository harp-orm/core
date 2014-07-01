<?php

namespace Harp\Core\Test\Model;

use Harp\Core\Model\AbstractModel;
use Harp\Core\Model\InheritedTrait;
use Harp\Core\Test\Repo;

/**
 * @author     Ivan Kerin <ikerin@gmail.com>
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://spdx.org/licenses/BSD-3-Clause
 */
class Post extends AbstractModel
{
    const REPO = 'Harp\Core\Test\Repo\Post';

    use InheritedTrait;

    public $id;
    public $name;
    public $body;
    public $userId;

    public function getUser()
    {
        return $this->getLinkedModel('user');
    }

    public function setUser(User $user)
    {
        $this->setLinkedModel('user', $user);

        return $this;
    }
}
