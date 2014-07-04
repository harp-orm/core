<?php

namespace Harp\Core\Test\Model;

use Harp\Core\Test\Rel;
use Harp\Core\Repo\AbstractRepo;

/**
 * @author     Ivan Kerin <ikerin@gmail.com>
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://spdx.org/licenses/BSD-3-Clause
 */
class BlogPost extends Post {

    public static function initialize(AbstractRepo $repo)
    {
        parent::initialize($repo);

        $repo
            ->setRootRepo(Post::getRepo())
            ->addRels([
                new Rel\One('address', $repo, Address::getRepo()),
            ]);
    }

    public $url;

    public function getAddress()
    {
        return $this->get('address');
    }

    public function setAddress(Address $address)
    {
        $this->set('address', $address);

        return $this;
    }
}
