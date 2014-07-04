<?php

namespace Harp\Core\Test\Unit\Repo;

use Harp\Core\Test\Model\AbstractTestModel;
use Harp\Core\Repo\AbstractRepo;
use Harp\Core\Model\InheritedTrait;

/**
 * @author     Ivan Kerin <ikerin@gmail.com>
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://spdx.org/licenses/BSD-3-Clause
 */
class Model extends AbstractTestModel
{
    use InheritedTrait;

    public static function initialize(AbstractRepo $repo)
    {
        InheritedTrait::initialize($repo);
    }

    public $id;
    public $name = 'test';
}
