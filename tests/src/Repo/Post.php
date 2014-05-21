<?php

namespace CL\LunaCore\Test\Repo;

use CL\LunaCore\Test\Rel;
use CL\LunaCore\Test\Model;
use CL\Carpo\Assert;

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
            self::$instance = new Post('CL\LunaCore\Test\Model\Post', 'Post.json');
        }

        return self::$instance;
    }

    public function initialize()
    {
        $this
            ->setInherited(true)
            ->setRels([
                new Rel\One('user', $this, Post::get()),
            ]);
    }
}
