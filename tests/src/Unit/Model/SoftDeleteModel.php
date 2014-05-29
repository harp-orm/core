<?php

namespace Harp\Core\Test\Unit\Model;

use Harp\Core\Model\AbstractModel;
use Harp\Core\Model\SoftDeleteTrait;

class SoftDeleteModel extends AbstractModel
{
    use SoftDeleteTrait;

    public function getRepo()
    {
        return SoftDeleteRepo::get();
    }
}
