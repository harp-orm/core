<?php

namespace Harp\Core\Test\Unit\Model;

use Harp\Core\Model\UnmappedPropertiesTrait;

/**
 * @author     Ivan Kerin <ikerin@gmail.com>
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://spdx.org/licenses/BSD-3-Clause
 */
class TestClassUnmapped
{
    use UnmappedPropertiesTrait;

    public $test1 = 'test1';
    public $test2 = 'test2';
}
