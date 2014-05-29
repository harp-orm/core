<?php

namespace Harp\Core\Test\Repo;

use Harp\Core\Test\Model;
use Harp\Core\Test\Rel;

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
            self::$instance = new BlogPost('Harp\Core\Test\Model\BlogPost', 'Post.json');
        }

        return self::$instance;
    }

    public function initialize()
    {
        parent::initialize();

        $this
            ->addRels([
                new Rel\One('address', $this, Address::get()),
            ]);
    }
}
