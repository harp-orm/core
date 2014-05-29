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

    private static $instance;

    /**
     * @return User
     */
    public static function get()
    {
        if (! self::$instance) {
            self::$instance = new Post('Harp\Core\Test\Model\Post', 'Post.json');
        }

        return self::$instance;
    }

    public function initialize()
    {
        $this
            ->setInherited(true)
            ->addRels([
                new Rel\One('user', $this, Post::get()),
            ]);
    }
}
