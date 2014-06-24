<?php

namespace Harp\Core\Test\Unit\Model;

use Harp\Core\Model\PropertiesAccessorTrait;

/**
 * @author     Ivan Kerin <ikerin@gmail.com>
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
class TestClassAccessor
{
    use PropertiesAccessorTrait;

    private $private1 = 'test4';

    protected $protected1 = 'test3';

    public $public1 = 'test1';

    public $public2 = 'test2';
}
