<?php

namespace CL\LunaCore\Test\Unit\Model;

use CL\LunaCore\Model\PropertiesAccessorTrait;

class TestClassAccessor
{
    use PropertiesAccessorTrait;

    private $private1 = 'test4';

    protected $protected1 = 'test3';

    public $public1 = 'test1';

    public $public2 = 'test2';
}
