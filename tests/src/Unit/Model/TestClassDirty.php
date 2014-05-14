<?php

namespace CL\LunaCore\Test\Unit\Model;

use CL\LunaCore\Model\DirtyTrackingTrait;

class TestClassDirty
{
    use DirtyTrackingTrait;

    public $test = 'test1';
    public $test2 = 'test2';
}
