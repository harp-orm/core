<?php

namespace Harp\Core\Test\Repo;

use Harp\Core\Test\Rel;
use Harp\Core\Test\Model;
use Harp\Validate\Assert;

/**
 * @author     Ivan Kerin <ikerin@gmail.com>
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://spdx.org/licenses/BSD-3-Clause
 */
class Post extends AbstractTestRepo {

    public function initialize()
    {
        $this
            ->setModelClass('Harp\Core\Test\Model\Post')
            ->setFile('Post.json')
            ->setInherited(true)
            ->addRels([
                new Rel\One('user', $this, User::get()),
            ]);
    }
}
