<?php

namespace Harp\Core\Test\Unit\Rel;

use Harp\Core\Repo\AbstractRepo;

/**
 * @author     Ivan Kerin <ikerin@gmail.com>
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
class Repo extends AbstractRepo
{
    public $test;

    public function initialize()
    {
        $this->setModelClass(__NAMESPACE__.'\Model');
    }
}
