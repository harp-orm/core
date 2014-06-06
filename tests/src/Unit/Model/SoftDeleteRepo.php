<?php

namespace Harp\Core\Test\Unit\Model;

use Harp\Core\Repo\AbstractRepo;

class SoftDeleteRepo extends AbstractRepo
{
    public static function newInstance()
    {
        return new SoftDeleteRepo(__NAMESPACE__.'\SoftDeleteModel');
    }

    public function initialize()
    {
        $this
            ->setSoftDelete(true);
    }
}
