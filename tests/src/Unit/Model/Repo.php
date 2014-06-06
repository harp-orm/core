<?php

namespace Harp\Core\Test\Unit\Model;

use Harp\Core\Repo\AbstractRepo;
use Harp\Validate\Assert\Present;

class Repo extends AbstractRepo
{
    public static function newInstance()
    {
        return new Repo(__NAMESPACE__.'\Model', 'Model.json');;
    }

    public function initialize()
    {
        $this
            ->setInherited(true)
            ->setAsserts([
                new Present('name'),
                new Present('other'),
            ]);
    }
}
