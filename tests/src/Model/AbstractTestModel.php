<?php

namespace Harp\Core\Test\Model;

use Harp\Core\Model\AbstractModel;
use Harp\Core\Test\Repo\Find;
use Harp\Core\Test\Repo\TestRepo;

/**
 * @author     Ivan Kerin <ikerin@gmail.com>
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://spdx.org/licenses/BSD-3-Clause
 */
abstract class AbstractTestModel extends AbstractModel
{
    public static function findAll()
    {
        return new Find(self::getRepo());
    }

    public static function newRepo($class)
    {
        return new TestRepo($class);
    }
}
