<?php

namespace CL\LunaCore\Test\Unit\Model;

use CL\LunaCore\Model\AbstractModel;
use CL\LunaCore\Model\SoftDeleteTrait;

class SoftDeleteModel extends AbstractModel
{
    use SoftDeleteTrait;

    public function getRepo()
    {
        return SoftDeleteRepo::get();
    }
}
