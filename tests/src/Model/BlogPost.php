<?php

namespace Harp\Core\Test\Model;

use Harp\Core\Test\Repo;

/**
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
class BlogPost extends Post {

    public function getRepo()
    {
        return Repo\BlogPost::get();
    }

    public $url;

    public function getAddress()
    {
        return Repo\Post::get()->loadLink($this, 'address')->get();
    }

    public function setAddress(Address $address)
    {
        return Repo\Post::get()->loadLink($this, 'address')->set($address);
    }
}
