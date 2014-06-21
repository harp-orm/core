<?php

namespace Harp\Core\Test\Unit\Model;

use Harp\Core\Model\AbstractModel;
use Harp\Core\Model\InheritedTrait;

class InheritedModel extends AbstractModel
{
    use InheritedTrait;

    public function getRepo()
    {
        return InheritedRepo::get();
    }
}
