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
class User extends AbstractTestRepo {

    public static function newInstance()
    {
        return new User('Harp\Core\Test\Model\User', 'User.json');
    }

    public function initialize()
    {
        $this
            ->addRels([
                new Rel\One('address', $this, Address::get()),
                (new Rel\Many('posts', $this, Post::get()))
                    ->setLinkClass(__NAMESPACE__.'\LinkManyPosts'),
            ])
            ->setSoftDelete(true)
            ->setAsserts([
                new Assert\Present('name'),
            ]);
    }
}
