<?php

namespace Harp\Core\Test\Repo;

use Harp\Core\Test\Rel;
use Harp\Core\Test\Model;
use Harp\Validate\Assert;

/**
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
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
