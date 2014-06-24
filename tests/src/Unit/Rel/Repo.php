<?php

namespace Harp\Core\Test\Unit\Rel;

use Harp\Core\Repo\AbstractRepo;

/**
 * @author     Ivan Kerin <ikerin@gmail.com>
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://spdx.org/licenses/BSD-3-Clause
 */
class Repo extends AbstractRepo
{
    public $test;

    public function initialize()
    {
        $this->setModelClass(__NAMESPACE__.'\Model');
    }
}
