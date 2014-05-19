<?php

namespace CL\LunaCore\Test\Repo;

use CL\LunaCore\Test\Model;
use CL\LunaCore\Test\Rel;

/**
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
class BlogPost extends Post {

    private static $instance;

    /**
     * @return User
     */
    public static function get()
    {
        if (! self::$instance) {
            self::$instance = new BlogPost(Model\BlogPost::class, 'Post.json');
        }

        return self::$instance;
    }

    public function initialize()
    {
        parent::initialize();

        $this
            ->setRels([
                new Rel\One('address', $this, Address::get()),
            ]);
    }
}
