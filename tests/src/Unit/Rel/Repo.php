<?php

namespace Harp\Core\Test\Unit\Rel;

use Harp\Core\Repo\AbstractRepo;

class Repo extends AbstractRepo
{
    public static function newInstance()
    {
        return new Repo(__NAMESPACE__.'\Model');
    }


    public $test;

    public function initialize()
    {

    }
}
