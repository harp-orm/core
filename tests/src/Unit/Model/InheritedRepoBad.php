<?php

namespace Harp\Core\Test\Unit\Model;

use Harp\Core\Repo\AbstractRepo;

class InheritedRepoBad extends AbstractRepo
{
    public static function newInstance()
    {
        return new InheritedRepoBad(__NAMESPACE__.'\InheritedModelBad');
    }

    public function initialize()
    {
        $this
            ->setInherited(false);
    }
}
