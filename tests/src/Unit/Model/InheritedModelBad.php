<?php

namespace Harp\Core\Test\Unit\Model;

use Harp\Core\Repo\AbstractRepo;
use Harp\Core\Model\InheritedTrait;
use Harp\Core\Test\Model\AbstractTestModel;

/**
 * @author     Ivan Kerin <ikerin@gmail.com>
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://spdx.org/licenses/BSD-3-Clause
 */
class InheritedModelBad extends AbstractTestModel
{
    use InheritedTrait;

    public static function initialize(AbstractRepo $repo)
    {
    }
}
