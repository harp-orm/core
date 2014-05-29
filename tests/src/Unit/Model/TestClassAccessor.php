<?php

namespace Harp\Core\Test\Unit\Model;

use Harp\Core\Model\PropertiesAccessorTrait;

class TestClassAccessor
{
    use PropertiesAccessorTrait;

    private $private1 = 'test4';

    protected $protected1 = 'test3';

    public $public1 = 'test1';

    public $public2 = 'test2';
}
