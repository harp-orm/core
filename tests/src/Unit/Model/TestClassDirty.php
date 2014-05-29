<?php

namespace Harp\Core\Test\Unit\Model;

use Harp\Core\Model\DirtyTrackingTrait;

class TestClassDirty
{
    use DirtyTrackingTrait;

    public $test = 'test1';
    public $test2 = 'test2';
}
