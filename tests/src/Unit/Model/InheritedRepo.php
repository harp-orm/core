<?php

namespace Harp\Core\Test\Unit\Model;

use Harp\Core\Repo\AbstractRepo;

class InheritedRepo extends AbstractRepo
{
    public static function newInstance()
    {
        return new InheritedRepo(__NAMESPACE__.'\InheritedModel');
    }

    public function initialize()
    {
        $this
            ->setInherited(true);
    }
}
