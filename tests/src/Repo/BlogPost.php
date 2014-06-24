<?php

namespace Harp\Core\Test\Repo;

use Harp\Core\Test\Model;
use Harp\Core\Test\Rel;

/**
 * @author     Ivan Kerin <ikerin@gmail.com>
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://spdx.org/licenses/BSD-3-Clause
 */
class BlogPost extends Post {

    public function initialize()
    {
        parent::initialize();

        $this
            ->setModelClass('Harp\Core\Test\Model\BlogPost')
            ->setRootRepo(Post::get())
            ->addRels([
                new Rel\One('address', $this, Address::get()),
            ]);
    }
}
