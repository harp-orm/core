<?php

namespace Harp\Core\Test\Unit\Model;

use Harp\Core\Model\DirtyTrackingTrait;

/**
 * @author     Ivan Kerin <ikerin@gmail.com>
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://spdx.org/licenses/BSD-3-Clause
 */
class TestClassDirty
{
    use DirtyTrackingTrait;

    public $test = 'test1';
    public $test2 = 'test2';
}
