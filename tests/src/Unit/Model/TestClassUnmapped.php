<?php

namespace Harp\Core\Test\Unit\Model;

use Harp\Core\Model\UnmappedPropertiesTrait;

class TestClassUnmapped
{
    use UnmappedPropertiesTrait;

    public $test1 = 'test1';
    public $test2 = 'test2';
}
