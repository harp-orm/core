<?php

namespace CL\LunaCore\Test\Unit\Model;

use CL\LunaCore\Model\UnmappedPropertiesTrait;

class TestClassUnmapped
{
    use UnmappedPropertiesTrait;

    public $test1 = 'test1';
    public $test2 = 'test2';
}
