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

    public static function newInstance()
    {
        return new Post('Harp\Core\Test\Model\Post', 'Post.json');
    }

    public function initialize()
    {
        $this
            ->setInherited(true)
            ->addRels([
                new Rel\One('user', $this, User::get()),
            ]);
    }
}
