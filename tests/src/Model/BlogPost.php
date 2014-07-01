<?php

namespace Harp\Core\Test\Model;

use Harp\Core\Test\Repo;

/**
 * @author     Ivan Kerin <ikerin@gmail.com>
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://spdx.org/licenses/BSD-3-Clause
 */
class BlogPost extends Post {

    const REPO = 'Harp\Core\Test\Repo\BlogPost';

    public $url;

    public function getAddress()
    {
        return $this->getLinkedModel('address');
    }

    public function setAddress(Address $address)
    {
        $this->setLinkedModel('address', $address);

        return $this;
    }
}
