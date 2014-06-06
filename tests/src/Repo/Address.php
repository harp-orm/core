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
class Address extends AbstractTestRepo {

    public static function newInstance()
    {
        return new Address('Harp\Core\Test\Model\Address', 'Address.json');
    }

    public function initialize()
    {
        $this
            ->addRels([
                new Rel\One('user', $this, Address::get()),
            ]);
    }
}
