<?php

namespace Harp\Core\Test\Repo;

use Harp\Core\Test\Model;
use Harp\Core\Test\Rel;

/**
 * @author     Ivan Kerin <ikerin@gmail.com>
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
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
