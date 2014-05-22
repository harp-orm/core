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
class User extends AbstractTestRepo {

    private static $instance;

    /**
     * @return User
     */
    public static function get()
    {
        if (! self::$instance) {
            self::$instance = new User('CL\LunaCore\Test\Model\User', 'User.json');
        }

        return self::$instance;
    }

    public function initialize()
    {
        $this
            ->addRels([
                new Rel\One('address', $this, Address::get()),
                new Rel\Many('posts', $this, Post::get()),
            ])
            ->setSoftDelete(true)
            ->setAsserts([
                new Assert\Present('name'),
            ]);
    }
}
